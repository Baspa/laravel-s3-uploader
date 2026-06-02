<?php

namespace Baspa\LaravelS3Uploader\Tests;

use Baspa\LaravelS3Uploader\LaravelS3UploaderServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelS3UploaderServiceProvider::class,
        ];
    }
}
