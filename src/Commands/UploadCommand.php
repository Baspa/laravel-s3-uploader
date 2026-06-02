<?php

namespace Baspa\LaravelS3Client\Commands;

use Baspa\LaravelS3Client\DirectoryUploader;
use Baspa\LaravelS3Client\UploadResult;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Throwable;

class UploadCommand extends Command
{
    public $signature = 's3:upload
        {source : The local directory to upload}
        {destination : The destination folder (prefix) on the disk}
        {--disk= : The disk to upload to (defaults to the s3-client.disk config value)}
        {--force : Overwrite files that already exist on the disk}
        {--dry-run : Show what would be uploaded without writing anything}';

    public $description = 'Upload a local folder to S3';

    public function handle(): int
    {
        $source = $this->stringArgument('source');
        $destination = $this->stringArgument('destination');
        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');

        $uploader = new DirectoryUploader(Storage::disk($this->diskName()));

        try {
            $files = $uploader->collect($source);
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($files === []) {
            $this->warn("No files found in [{$source}].");

            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info('Dry run — no files will be uploaded.');
        }

        $result = new UploadResult;
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $relativePath) {
            try {
                $result->record($uploader->store($source, $relativePath, $destination, $force, $dryRun));
            } catch (Throwable $e) {
                $result->recordFailure($relativePath, $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->line(sprintf(
            '%s: %d, Skipped: %d, Failed: %d',
            $dryRun ? 'Would upload' : 'Uploaded',
            $result->uploaded,
            $result->skipped,
            count($result->failed),
        ));

        foreach ($result->failed as $path => $error) {
            $this->error("Failed: {$path} — {$error}");
        }

        return $result->hasFailures() ? self::FAILURE : self::SUCCESS;
    }

    private function stringArgument(string $name): string
    {
        $value = $this->argument($name);

        return is_string($value) ? $value : '';
    }

    private function diskName(): string
    {
        $disk = $this->option('disk');

        if (is_string($disk) && $disk !== '') {
            return $disk;
        }

        $configured = config('s3-client.disk');

        return is_string($configured) ? $configured : 's3';
    }
}
