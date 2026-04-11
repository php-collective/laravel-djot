<?php

declare(strict_types=1);

namespace PhpCollective\LaravelDjot\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PhpCollective\LaravelDjot\Facades\Djot;
use PhpCollective\LaravelDjot\LaravelDjotServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDjotServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Djot' => Djot::class,
        ];
    }
}
