<?php

namespace Baspa\LaravelS3Client;

class UploadResult
{
    public int $uploaded = 0;

    public int $skipped = 0;

    /**
     * Map of relative path => error message for files that failed to upload.
     *
     * @var array<string, string>
     */
    public array $failed = [];

    public function record(UploadStatus $status): void
    {
        match ($status) {
            UploadStatus::Uploaded => $this->uploaded++,
            UploadStatus::Skipped => $this->skipped++,
        };
    }

    public function recordFailure(string $relativePath, string $error): void
    {
        $this->failed[$relativePath] = $error;
    }

    public function total(): int
    {
        return $this->uploaded + $this->skipped + count($this->failed);
    }

    public function hasFailures(): bool
    {
        return $this->failed !== [];
    }
}
