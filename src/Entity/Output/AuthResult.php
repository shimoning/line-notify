<?php

namespace Shimoning\LineNotify\Entity\Output;

class AuthResult
{
    private string $code;
    private string $state;

    public function __construct(string $code, string $state)
    {
        $this->code = $code;
        $this->state = $state;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function isSucceeded(): bool
    {
        return true;
    }
}
