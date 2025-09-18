<?php

namespace Alikhosravidev\VerboseValidator\Tests;

use Alikhosravidev\VerboseValidator\VerboseValidatorServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            VerboseValidatorServiceProvider::class,
        ];
    }
}