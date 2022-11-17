<?php

namespace Shimoning\LineNotify\ValueObject;

use Shimoning\LineNotify\Exceptions\ValidationException;

class ImageUri
{
    private string $value;

    public function __construct(string $value, bool $restrict = true)
    {
        if (!$this->isValid($value, $restrict)) {
            throw new ValidationException('画像URIが正しくありません。');
        }
        $this->value = $value;
    }

    protected function isValid(string $value, bool $restrict = true): bool
    {
        if (empty($value) || ! \preg_match('/^https?:\/\//', $value)) {
            return false;
        }

        if ($restrict) {
            $extension = \pathinfo($value, \PATHINFO_EXTENSION);
            if (! \in_array($extension, ['jpeg', 'jpg'])) {
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
        return \value_get_contents($this->value);
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
