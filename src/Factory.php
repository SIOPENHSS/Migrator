<?php

namespace SIOPEN\Migrator;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Factory
{
    /**
     * @var string
     */
    protected string $origin;

    /**
     * @var array
     */
    protected array $uniques = [];

    /**
     * @var Command|null
     */
    protected Command|null $console = null;

    /**
     * @var Closure|null
     */
    protected Closure|null $callback = null;

    /**
     * @param  array  $fields
     * @param  string $connection
     */
    public function __construct(protected array $fields, protected string $connection = 'old_siopen')
    {
        $this->fields = array_merge(
            [
                'created_at' => 'created_at',
                'updated_at' => 'updated_at',
                'deleted_at' => 'deleted_at',
            ],
            $this->fields,
        );
    }

    /**
     * @param  Command $console
     * @return $this
     */
    public function console(Command $console) : static
    {
        $this->console = $console;

        return $this;
    }

    /**
     * @param  array $uniques
     * @return $this
     */
    public function uniques(array $uniques = []) : static
    {
        $this->uniques = $uniques;

        return $this;
    }

    /**
     * @param  Closure $callback
     * @return $this
     */
    public function created(Closure $callback) : static
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @param  string      $class
     * @param  string|null $origin
     * @return void
     */
    public function migrate(string $class, mixed $origin = null) : void
    {
        if ($origin) {
            $this->origin = is_array($origin) ? array_keys($origin)[0] : $origin;
        }

        /**
         * @var Collection $collections
         */
        $query = $this->origin::on($this->connection);

        if (is_array($origin)) {
            $query = $origin[$this->origin]($query);
        }

        if (in_array(SoftDeletes::class, class_uses_recursive($this->origin))) {
            $query = $query->withTrashed();
        }

        $callback = function ($result) use ($class) {
            $this->create($result, $class);
        };

        $this->console->newLine();
        $this->console->info('MIGRATING : ' . $this->origin . ' => ' . $class);
        $query->chunk(100, function ($items) use ($callback) {
            $this->console->withProgressBar($items, $callback);
            $this->console->newLine();
        });

        $this->console->newLine();
    }

    /**
     * @param  mixed $origin
     * @param  mixed $class
     * @return void
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function create(mixed $origin, mixed $class) : void
    {
        /**
         * @var Model $class
         */

        $data = $this->populate($origin);

        if (empty($this->uniques)) {
            $result = is_string($class) ? $class::create($data->toArray()) : $class->create($data->toArray());
        } else {
            $uniques = [];
            foreach ($this->uniques as $key => $originKey) {
                if (is_int($key) && is_string($originKey)) {
                    $key = $originKey;
                    $uniques[$key] = $data->get($originKey);
                } else if (is_string($key) && is_callable($originKey)) {
                    $uniques[$key] = $originKey($data);
                }
            }

            $result = is_string($class) ? $class::withTrashed()->updateOrCreate($uniques, $data->toArray()) : $class->updateOrCreate($uniques, $data->toArray());
        }

        if ($this->callback) {
            call_user_func_array($this->callback, [$result, $origin]);
        }
    }

    /**
     * @param  mixed $origin
     * @return Collection
     */
    public function populate(mixed $origin) : Collection
    {
        if (is_null($origin)) {
            return collect([
                //
            ]);
        }

        $data = [];
        foreach ($this->fields as $key => $original) {
            if (is_string($key) && is_string($original)) {
                $data[$key] = $origin->getOriginal($original);
            } else if (is_string($key) && is_callable($original)) {
                $data[$key] = $original($origin);
            } else {
                $data[$original] = $origin->getOriginal($original);
            }
        }

        return collect($data);
    }
}
