<?php

namespace Shimoning\LineNotify\Entities\Input;

use Shimoning\LineNotify\Exceptions\ImageFileMissingException;
use Shimoning\LineNotify\Exceptions\ImagePairMissingException;

class Image
{
    private string|null $thumbnail = null;
    private string|null $fullSize = null;
    private string|null $filePath = null;

    public function __construct(
        ?string $thumbnail = null,
        ?string $fullSize = null,
        ?string $filePath = null,
    ) {
        if ($thumbnail || $fullSize) {
            if ($thumbnail && !$fullSize || !$thumbnail && $fullSize) {
                throw new ImagePairMissingException();
            }
            // TODO: check jpeg only
            $this->thumbnail = $thumbnail;
            $this->fullSize = $fullSize;
        }

        if ($filePath) {
            if (!\file_exists($filePath)) {
                throw new ImageFileMissingException();
            }
            // TODO: check png, jpeg only
            $this->filePath = $filePath;
        }
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function getFullSize(): ?string
    {
        return $this->fullSize;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function hasImage(): bool
    {
        return !empty($this->thumbnail) && !empty($this->thumbnail);
    }

    public function hasFile(): bool
    {
        return !empty($this->filePath) && \file_exists($this->filePath);
    }

    public function getBinaryFile()
    {
        // return \file_get_contents($this->filePath);
        return \fopen($this->filePath, 'r');
    }
}
