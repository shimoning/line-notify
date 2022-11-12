<?php

namespace Shimoning\LineNotify\Entities\Input;

use Shimoning\LineNotify\Exceptions\MissingImageFileException;
use Shimoning\LineNotify\Exceptions\MissingImagePairException;

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
            if (($thumbnail && !$fullSize) || (!$thumbnail && $fullSize)) {
                throw new MissingImagePairException();
            }
            // TODO: check jpeg only, size <= 240×240px
            $this->thumbnail = $thumbnail;
            $this->fullSize = $fullSize;
        }

        if ($filePath) {
            if (!\file_exists($filePath)) {
                throw new MissingImageFileException();
            }
            // TODO: check png, jpeg only, size <= 2048×2048px
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
