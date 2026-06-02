<?php

namespace Baspa\LaravelS3Uploader;

enum UploadStatus
{
    case Uploaded;
    case Skipped;
}
