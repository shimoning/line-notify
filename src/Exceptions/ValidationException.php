<?php

namespace Shimoning\LineNotify\Exceptions;

class ValidationException extends LineNotifyException
{
    protected $message = 'リクエストが不正です。';
}
