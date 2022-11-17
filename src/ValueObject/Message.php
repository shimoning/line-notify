<?php

namespace Shimoning\LineNotify\ValueObject;

use Shimoning\LineNotify\Exceptions\ValidationException;

class Message
{
    private string $value;

    public function __construct(string $value)
    {
        if (!$this->isValid($value)) {
            throw new ValidationException('message は 1000 文字以下にしてください。');
        }
        $this->value = $value;
    }

    protected function isValid(string $value): bool
    {
        return \mb_strlen($value) <= 1000;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
