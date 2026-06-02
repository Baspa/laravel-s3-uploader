<?php

use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->source = tempDir();
});

afterEach(function () {
    removeDir($this->source);
});

it('uploads every file in the source folder to the destination', function () {
    makeFile($this->source, 'a.txt', 'A');
    makeFile($this->source, 'sub/b.txt', 'B');
    $disk = Storage::fake('s3');

    $this->artisan('s3:upload', [
        'source' => $this->source,
        'destination' => 'backups',
    ])->assertSuccessful();

    $disk->assertExists('backups/a.txt');
    $disk->assertExists('backups/sub/b.txt');
});

it('fails with an error when the source does not exist', function () {
    Storage::fake('s3');

    $this->artisan('s3:upload', [
        'source' => $this->source.'/missing',
        'destination' => 'backups',
    ])->assertFailed();
});

it('skips existing files by default and reports the counts', function () {
    makeFile($this->source, 'a.txt', 'new');
    $disk = Storage::fake('s3');
    $disk->put('backups/a.txt', 'old');

    $this->artisan('s3:upload', [
        'source' => $this->source,
        'destination' => 'backups',
    ])
        ->expectsOutputToContain('Skipped: 1')
        ->assertSuccessful();

    expect($disk->get('backups/a.txt'))->toBe('old');
});

it('overwrites existing files with --force', function () {
    makeFile($this->source, 'a.txt', 'new');
    $disk = Storage::fake('s3');
    $disk->put('backups/a.txt', 'old');

    $this->artisan('s3:upload', [
        'source' => $this->source,
        'destination' => 'backups',
        '--force' => true,
    ])->assertSuccessful();

    expect($disk->get('backups/a.txt'))->toBe('new');
});

it('writes nothing on a --dry-run', function () {
    makeFile($this->source, 'a.txt', 'A');
    $disk = Storage::fake('s3');

    $this->artisan('s3:upload', [
        'source' => $this->source,
        'destination' => 'backups',
        '--dry-run' => true,
    ])->assertSuccessful();

    $disk->assertMissing('backups/a.txt');
});

it('uploads to the disk given by --disk', function () {
    makeFile($this->source, 'a.txt', 'A');
    Storage::fake('s3');
    $other = Storage::fake('archive');

    $this->artisan('s3:upload', [
        'source' => $this->source,
        'destination' => 'backups',
        '--disk' => 'archive',
    ])->assertSuccessful();

    $other->assertExists('backups/a.txt');
});

it('reports failures and exits non-zero when a file cannot be read', function () {
    makeFile($this->source, 'ok.txt', 'A');
    makeFile($this->source, 'bad.txt', 'B');
    chmod($this->source.'/bad.txt', 0000);

    if (is_readable($this->source.'/bad.txt')) {
        $this->markTestSkipped('Unable to make a file unreadable (likely running as root).');
    }

    $disk = Storage::fake('s3');

    $this->artisan('s3:upload', [
        'source' => $this->source,
        'destination' => 'backups',
    ])
        ->expectsOutputToContain('Failed: 1')
        ->assertFailed();

    $disk->assertExists('backups/ok.txt');
});

it('warns and succeeds when the source folder is empty', function () {
    Storage::fake('s3');

    $this->artisan('s3:upload', [
        'source' => $this->source,
        'destination' => 'backups',
    ])->assertSuccessful();
});
