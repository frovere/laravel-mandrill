<?php

namespace Felrov\Drill\Tests;

use Felrov\Drill\MandrillServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [MandrillServiceProvider::class];
    }
}
