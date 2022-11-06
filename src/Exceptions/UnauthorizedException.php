<?php

namespace Shimoning\LineNotify\Exceptions;

class UnauthorizedException extends LineNotifyException
{
    protected $message = '認証エラー';
}
