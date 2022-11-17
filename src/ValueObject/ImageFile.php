<?php

namespace Shimoning\LineNotify\ValueObject;

use Shimoning\LineNotify\Exceptions\MissingImageFileException;

class ImageFile
{
    private string $value;

    public function __construct(string $value)
    {
        if (!$this->isValid($value)) {
            throw new MissingImageFileException();
        }
        $this->value = $value;
    }

    protected function isValid(string $value): bool
    {
        if (!\file_exists($value)) {
            return false;
        }

        $extension = \pathinfo($value, \PATHINFO_EXTENSION);
        if (! \in_array($extension, ['jpeg', 'jpg', 'png'])) {
            return false;
        }
        return true;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getBinary()
    {
        // return \value_get_contents($this->value);
        return \fopen($this->value, 'r');
    }

    public function getExtension(): string
    {
        return \pathinfo($this->value, \PATHINFO_EXTENSION);
    }

    public function getFilename(): string
    {
        return \pathinfo($this->value, \PATHINFO_FILENAME);
    }
}
