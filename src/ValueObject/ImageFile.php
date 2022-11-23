<?php

namespace Shimoning\LineNotify\ValueObject;

use Shimoning\LineNotify\Exceptions\MissingImageFileException;

class ImageFile
{
    private string $value;

    public function __construct(string $value, bool $restrict = true)
    {
        if (!$this->isValid($value, $restrict)) {
            throw new MissingImageFileException();
        }
        $this->value = $value;
    }

    protected function isValid(string $value, bool $restrict = true): bool
    {
        if (!\file_exists($value)) {
            return false;
        }

        if ($restrict) {
            $extension = \pathinfo($value, \PATHINFO_EXTENSION);
            if (! \in_array($extension, ['jpeg', 'jpg', 'png'])) {
                return false;
            }
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
