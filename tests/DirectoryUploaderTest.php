<?php

use Baspa\LaravelS3Uploader\DirectoryUploader;
use Baspa\LaravelS3Uploader\Tests\Doubles\StreamClosingDisk;
use Baspa\LaravelS3Uploader\UploadStatus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->source = tempDir();
});

afterEach(function () {
    removeDir($this->source);
});

it('collects all files recursively as sorted relative paths', function () {
    makeFile($this->source, 'a.txt');
    makeFile($this->source, 'sub/b.txt');
    makeFile($this->source, 'sub/deep/c.txt');

    $uploader = new DirectoryUploader(Storage::fake('s3'));

    expect($uploader->collect($this->source))->toBe([
        'a.txt',
        'sub/b.txt',
        'sub/deep/c.txt',
    ]);
});

it('throws when the source directory does not exist', function () {
    $uploader = new DirectoryUploader(Storage::fake('s3'));

    expect(fn () => $uploader->collect($this->source.'/nope'))
        ->toThrow(InvalidArgumentException::class);
});

it('uploads a file under the destination prefix, preserving structure', function () {
    makeFile($this->source, 'sub/b.txt', 'hello');
    $disk = Storage::fake('s3');
    $uploader = new DirectoryUploader($disk);

    $status = $uploader->store($this->source, 'sub/b.txt', 'backups/2026');

    expect($status)->toBe(UploadStatus::Uploaded);
    $disk->assertExists('backups/2026/sub/b.txt');
    expect($disk->get('backups/2026/sub/b.txt'))->toBe('hello');
});

it('uploads to the bare relative path when destination is empty', function () {
    makeFile($this->source, 'a.txt', 'hi');
    $disk = Storage::fake('s3');
    $uploader = new DirectoryUploader($disk);

    $uploader->store($this->source, 'a.txt', '');

    $disk->assertExists('a.txt');
});

it('skips a file that already exists unless forced', function () {
    makeFile($this->source, 'a.txt', 'new');
    $disk = Storage::fake('s3');
    $disk->put('dest/a.txt', 'old');
    $uploader = new DirectoryUploader($disk);

    $status = $uploader->store($this->source, 'a.txt', 'dest');

    expect($status)->toBe(UploadStatus::Skipped);
    expect($disk->get('dest/a.txt'))->toBe('old');
});

it('overwrites an existing file when forced', function () {
    makeFile($this->source, 'a.txt', 'new');
    $disk = Storage::fake('s3');
    $disk->put('dest/a.txt', 'old');
    $uploader = new DirectoryUploader($disk);

    $status = $uploader->store($this->source, 'a.txt', 'dest', force: true);

    expect($status)->toBe(UploadStatus::Uploaded);
    expect($disk->get('dest/a.txt'))->toBe('new');
});

it('reports what would happen on a dry run without writing', function () {
    makeFile($this->source, 'a.txt', 'new');
    $disk = Storage::fake('s3');
    $uploader = new DirectoryUploader($disk);

    $status = $uploader->store($this->source, 'a.txt', 'dest', dryRun: true);

    expect($status)->toBe(UploadStatus::Uploaded);
    $disk->assertMissing('dest/a.txt');
});

it('reports a dry-run skip for an existing file', function () {
    makeFile($this->source, 'a.txt', 'new');
    $disk = Storage::fake('s3');
    $disk->put('dest/a.txt', 'old');
    $uploader = new DirectoryUploader($disk);

    $status = $uploader->store($this->source, 'a.txt', 'dest', dryRun: true);

    expect($status)->toBe(UploadStatus::Skipped);
});

it('uploads through adapters that close the stream themselves', function () {
    makeFile($this->source, 'a.txt', 'hi');
    $disk = Storage::fake('s3');
    $uploader = new DirectoryUploader(new StreamClosingDisk($disk));

    $status = $uploader->store($this->source, 'a.txt', 'dest');

    expect($status)->toBe(UploadStatus::Uploaded);
    $disk->assertExists('dest/a.txt');
    expect($disk->get('dest/a.txt'))->toBe('hi');
});
