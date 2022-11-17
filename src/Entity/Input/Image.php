<?php

namespace Shimoning\LineNotify\Entity\Input;

use Shimoning\LineNotify\ValueObject\ImageUri;
use Shimoning\LineNotify\ValueObject\ImageFile;
use Shimoning\LineNotify\Exceptions\MissingImagePairException;

class Image
{
    private ImageUri|null $thumbnail = null;
    private ImageUri|null $fullSize = null;
    private ImageFile|null $file = null;

    public function __construct(
        ?ImageUri $thumbnail = null,
        ?ImageUri $fullSize = null,
        ?ImageFile $file = null,
    ) {
        if ($thumbnail || $fullSize) {
            if (($thumbnail && !$fullSize) || (!$thumbnail && $fullSize)) {
                throw new MissingImagePairException();
            }
            // TODO: check jpeg only, size <= 240×240px, 2048×2048px
            $this->thumbnail = $thumbnail;
            $this->fullSize = $fullSize;
        }

        if ($file) {
            $this->file = $file;
        }
    }

    public function getThumbnail(): ?ImageUri
    {
        return $this->thumbnail;
    }

    public function getThumbnailUri(): ?string
    {
        return $this->thumbnail?->getValue();
    }

    public function getFullSize(): ?ImageUri
    {
        return $this->fullSize;
    }

    public function getFullSizeUri(): ?string
    {
        return $this->fullSize?->getValue();
    }

    public function getFile(): ?ImageFile
    {
        return $this->file;
    }

    public function hasUri(): bool
    {
        return !empty($this->thumbnail) && !empty($this->fullSize);
    }

    public function hasFile(): bool
    {
        return !empty($this->file);
    }

    public function getBinaryFile()
    {
        return $this->file->getBinary();
    }

    public function getFilename()
    {
        return $this->file->getFilename();
    }
}
