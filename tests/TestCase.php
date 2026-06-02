<?php

namespace Baspa\LaravelS3Client\Tests;

use Baspa\LaravelS3Client\LaravelS3ClientServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelS3ClientServiceProvider::class,
        ];
    }
}
