<?php

namespace Baspa\LaravelS3Uploader;

use FilesystemIterator;
use Illuminate\Contracts\Filesystem\Filesystem;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

class DirectoryUploader
{
    public function __construct(
        private Filesystem $disk
    ) {}

    /**
     * Return every file beneath the source directory as a sorted list of
     * relative, forward-slashed paths.
     *
     * @return list<string>
     */
    public function collect(string $source): array
    {
        if (! is_dir($source)) {
            throw new InvalidArgumentException("Source directory [{$source}] does not exist.");
        }

        $source = rtrim($source, '/');

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $files = [];

        foreach ($iterator as $file) {
            if (! $file instanceof SplFileInfo || ! $file->isFile()) {
                continue;
            }

            $relative = substr($file->getPathname(), strlen($source) + 1);
            $files[] = str_replace('\\', '/', $relative);
        }

        sort($files);

        return $files;
    }

    /**
     * Upload a single file to the destination prefix on the disk.
     *
     * Returns Skipped when the target already exists and $force is false.
     * On a dry run nothing is written, but the status that would result is
     * still returned. Throws if the local file cannot be read.
     */
    public function store(
        string $source,
        string $relativePath,
        string $destination,
        bool $force = false,
        bool $dryRun = false
    ): UploadStatus {
        $target = $this->remoteKey($destination, $relativePath);

        if ($this->disk->exists($target) && ! $force) {
            return UploadStatus::Skipped;
        }

        if (! $dryRun) {
            $localPath = rtrim($source, '/').'/'.$relativePath;
            $stream = fopen($localPath, 'r');

            if ($stream === false) {
                throw new RuntimeException("Unable to read local file [{$localPath}].");
            }

            try {
                $this->disk->writeStream($target, $stream);
            } finally {
                fclose($stream);
            }
        }

        return UploadStatus::Uploaded;
    }

    private function remoteKey(string $destination, string $relativePath): string
    {
        $destination = trim($destination, '/');

        return $destination === '' ? $relativePath : $destination.'/'.$relativePath;
    }
}
