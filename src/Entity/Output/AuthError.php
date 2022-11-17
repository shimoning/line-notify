<?php

namespace Shimoning\LineNotify\Entity\Output;

class AuthError
{
    private string $error;
    private string $errorDescription;

    public function __construct(string $error, string $errorDescription)
    {
        $this->error = $error;
        $this->errorDescription = $errorDescription;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getErrorDescription(): string
    {
        return $this->errorDescription;
    }
}
