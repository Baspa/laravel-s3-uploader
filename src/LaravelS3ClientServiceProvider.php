<?php

namespace Baspa\LaravelS3Client;

use Baspa\LaravelS3Client\Commands\UploadCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelS3ClientServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-s3-client')
            ->hasConfigFile('s3-client')
            ->hasCommand(UploadCommand::class);
    }
}
