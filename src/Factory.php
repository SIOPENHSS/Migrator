<?php

namespace SIOPEN\Migrator;

use Closure;
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
     * @var Closure|null
     */
    protected Closure|null $callback;

    /**
     * @param  string $connection
     * @param  array  $fields
     */
    public function __construct(protected string $connection = 'siopen', protected array $fields = [])
    {
        //
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
    public function migrate(string $class, string $origin = null) : void
    {
        if ($origin) {
            $this->origin = $origin;
        }

        $collections = $this->origin::on($this->connection)->get();

        $collections->each(function ($result) use ($class) {
            $this->create($result, $class);
        });
    }

    /**
     * @param  mixed  $origin
     * @param  string $class
     * @return void
     */
    protected function create(mixed $origin, string $class) : void
    {
        $data = $this->populate($origin);

        if (empty($this->uniques)) {
            $result = $class::create($data->toArray());
        } else {
            $uniques = [];
            foreach ($this->uniques as $key) {
                $uniques[$key] = $data->get($key);
            }

            $result = $class::updateOrCreate($uniques, $data->toArray());
        }

        if ($this->callback) {
            call_user_func_array($this->callback, [$result, $origin]);
        }
    }

    /**
     * @param  mixed $model
     * @return Collection
     */
    protected function populate(mixed $model) : Collection
    {
        $data = [];
        foreach ($this->fields as $key => $original) {
            if (is_string($original)) {
                $data[$key] = $model->getOriginal($original);
            } else if (is_callable($original)) {
                $data[$key] = $original($model);
            }
        }

        return collect($data);
    }
}
