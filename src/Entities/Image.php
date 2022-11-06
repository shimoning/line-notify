<?php

namespace Shimoning\LineNotify\Entities;

use Shimoning\LineNotify\Exceptions\ImageFileMissingException;

class Image
{
    private string|null $thumbnail;
    private string|null $fullSize;
    private string|null $filePath;

    public function __construct(
        string $thumbnail,
        string $fullSize,
        string $filePath,
    ) {
        $this->thumbnail = $thumbnail;
        $this->fullSize = $fullSize;

        if ($filePath) {
            if (!\file_exists($filePath)) {
                throw new ImageFileMissingException();
            }
            $this->filePath = $filePath;
        }
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function getFullSize(): string
    {
        return $this->fullSize;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function hasFile(): bool
    {
        return !empty($this->filePath) && \file_exists($this->filePath);
    }
}
