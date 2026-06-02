<?php

namespace Baspa\LaravelS3Uploader;

use Baspa\LaravelS3Uploader\Commands\UploadCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelS3UploaderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-s3-uploader')
            ->hasConfigFile('s3-uploader')
            ->hasCommand(UploadCommand::class);
    }
}
