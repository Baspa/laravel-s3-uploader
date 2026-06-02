<?php

use Baspa\LaravelS3Uploader\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Create a unique, empty temporary directory and return its absolute path.
 */
function tempDir(): string
{
    $dir = sys_get_temp_dir().'/s3client-'.uniqid('', true);
    mkdir($dir, 0777, true);

    return $dir;
}

/**
 * Write a file (creating parent directories) inside the given base directory.
 */
function makeFile(string $base, string $relativePath, string $contents = 'x'): void
{
    $full = $base.'/'.$relativePath;
    @mkdir(dirname($full), 0777, true);
    file_put_contents($full, $contents);
}

/**
 * Recursively delete a directory.
 */
function removeDir(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }

    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($items as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }

    rmdir($dir);
}
