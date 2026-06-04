<?php

namespace Baspa\LaravelS3Uploader\Tests\Doubles;

use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Decorates a disk and closes the resource passed to writeStream, mimicking
 * adapters such as league/flysystem-aws-s3-v3 that consume and close the
 * stream themselves.
 */
class StreamClosingDisk implements Filesystem
{
    public function __construct(private Filesystem $inner) {}

    public function writeStream($path, $resource, array $options = [])
    {
        $result = $this->inner->writeStream($path, $resource, $options);

        if (is_resource($resource)) {
            fclose($resource);
        }

        return $result;
    }

    public function path($path)
    {
        return $this->inner->path($path);
    }

    public function exists($path)
    {
        return $this->inner->exists($path);
    }

    public function get($path)
    {
        return $this->inner->get($path);
    }

    public function readStream($path)
    {
        return $this->inner->readStream($path);
    }

    public function put($path, $contents, $options = [])
    {
        return $this->inner->put($path, $contents, $options);
    }

    public function putFile($path, $file = null, $options = [])
    {
        return $this->inner->putFile($path, $file, $options);
    }

    public function putFileAs($path, $file, $name = null, $options = [])
    {
        return $this->inner->putFileAs($path, $file, $name, $options);
    }

    public function getVisibility($path)
    {
        return $this->inner->getVisibility($path);
    }

    public function setVisibility($path, $visibility)
    {
        return $this->inner->setVisibility($path, $visibility);
    }

    public function prepend($path, $data)
    {
        return $this->inner->prepend($path, $data);
    }

    public function append($path, $data)
    {
        return $this->inner->append($path, $data);
    }

    public function delete($paths)
    {
        return $this->inner->delete($paths);
    }

    public function copy($from, $to)
    {
        return $this->inner->copy($from, $to);
    }

    public function move($from, $to)
    {
        return $this->inner->move($from, $to);
    }

    public function size($path)
    {
        return $this->inner->size($path);
    }

    public function lastModified($path)
    {
        return $this->inner->lastModified($path);
    }

    public function files($directory = null, $recursive = false)
    {
        return $this->inner->files($directory, $recursive);
    }

    public function allFiles($directory = null)
    {
        return $this->inner->allFiles($directory);
    }

    public function directories($directory = null, $recursive = false)
    {
        return $this->inner->directories($directory, $recursive);
    }

    public function allDirectories($directory = null)
    {
        return $this->inner->allDirectories($directory);
    }

    public function makeDirectory($path)
    {
        return $this->inner->makeDirectory($path);
    }

    public function deleteDirectory($directory)
    {
        return $this->inner->deleteDirectory($directory);
    }
}
