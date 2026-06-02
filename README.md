# Laravel S3 Uploader

[![Latest Version on Packagist](https://img.shields.io/packagist/v/baspa/laravel-s3-uploader.svg?style=flat-square)](https://packagist.org/packages/baspa/laravel-s3-uploader)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/baspa/laravel-s3-uploader/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/baspa/laravel-s3-uploader/actions?query=workflow%3Arun-tests+branch%3Amain)
[![PHPStan](https://img.shields.io/github/actions/workflow/status/baspa/laravel-s3-uploader/phpstan.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/baspa/laravel-s3-uploader/actions?query=workflow%3APHPStan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/baspa/laravel-s3-uploader.svg?style=flat-square)](https://packagist.org/packages/baspa/laravel-s3-uploader)

Upload a local folder to S3 (or any S3-compatible storage) with a single Artisan command. It reads your existing S3 credentials straight from your application's filesystem configuration, walks a directory recursively, and streams every file to a destination prefix on the bucket.

```bash
php artisan s3:upload storage/app/exports backups/2026
```

## Installation

Install the package via composer:

```bash
composer require baspa/laravel-s3-uploader
```

This package uploads through a standard Laravel filesystem disk, so you also need the S3 driver in your application (most Laravel apps already have it):

```bash
composer require league/flysystem-aws-s3-v3 "^3.0"
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag="laravel-s3-uploader-config"
```

## Configuration

The package uploads to a disk defined in your application's `config/filesystems.php`. By default it uses the `s3` disk, which is configured through the usual environment variables:

```dotenv
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=eu-central-1
AWS_BUCKET=your-bucket
```

S3-compatible providers (MinIO, DigitalOcean Spaces, Cloudflare R2, …) work too — just set `AWS_ENDPOINT` on the `s3` disk as you normally would.

Want to point the package at a different disk? Publish the config and change the disk name, or set it in your `.env`:

```dotenv
S3_UPLOADER_DISK=backups
```

The published `config/s3-uploader.php`:

```php
return [
    'disk' => env('S3_UPLOADER_DISK', 's3'),
];
```

## Usage

```bash
php artisan s3:upload {source} {destination}
```

| Argument / option | Description |
|-------------------|-------------|
| `source`          | The local directory to upload. |
| `destination`     | The destination folder (prefix) on the disk. Use `""` to upload to the bucket root. |
| `--disk=`         | Upload to a specific disk instead of the configured default. |
| `--force`         | Overwrite files that already exist on the disk. Without it, existing files are skipped. |
| `--dry-run`       | Show what would be uploaded without writing anything. |

The directory is walked recursively and the folder structure is preserved under the destination prefix. For example, uploading a folder that contains `sub/report.pdf` to `backups/2026` stores it at `backups/2026/sub/report.pdf`.

### Examples

Upload a folder, skipping anything already in the bucket:

```bash
php artisan s3:upload storage/app/exports backups/2026
```

Re-upload everything, overwriting existing objects:

```bash
php artisan s3:upload storage/app/exports backups/2026 --force
```

Preview the upload without touching S3:

```bash
php artisan s3:upload storage/app/exports backups/2026 --dry-run
```

Upload to a different disk:

```bash
php artisan s3:upload storage/app/exports backups/2026 --disk=spaces
```

The command shows a progress bar while uploading and prints a summary afterwards, e.g. `Uploaded: 12, Skipped: 3, Failed: 0`. It exits with a non-zero status code if any file failed to upload, so it is safe to use in scripts and CI.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Bas van Dinther](https://github.com/Baspa)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
