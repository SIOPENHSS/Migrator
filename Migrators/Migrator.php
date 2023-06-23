<?php

namespace App\Laravel\Migrators;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use SIOPEN\Migrator\Factory;

abstract class Migrator
{
    public function __construct(protected Command $command)
    {
        //
    }

    public function factory(array $fields) : Factory
    {
        return (new Factory($fields))->console($this->command);
    }

    public function assertSame(mixed $expected, mixed $actual) : static
    {
        try {
            $two = in_array(SoftDeletes::class, class_uses_recursive($actual)) ? $actual::withTrashed()->count() : $actual::count();
            $one = in_array(SoftDeletes::class, class_uses_recursive($expected)) ? $expected::withTrashed()->count() : $expected::count();

            Assert::assertSame($one, $two);
        } catch (ExpectationFailedException $exception) {
            throw new ExpectationFailedException($expected . ' => ' . $exception->getMessage());
        }

        return $this;
    }

    abstract public function handle() : void;
}
