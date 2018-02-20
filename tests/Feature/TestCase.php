<?php

namespace Orchestra\Model\TestCase\Feature;

use Orchestra\Testbench\TestCase as Testbench;
use Illuminate\Auth\AuthServiceProvider as BaseServiceProvider;
use Orchestra\Auth\AuthServiceProvider as OverrideServiceProvider;

abstract class TestCase extends Testbench
{
    /**
     * Override application aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function overrideApplicationProviders($app): array
    {
        return [
            BaseServiceProvider::class => OverrideServiceProvider::class,
        ];
    }
}
