<?php

namespace Shimoning\LineNotify\Exceptions;

class MissingAccessTokenException extends LineNotifyException
{
    protected $message = 'アクセストークンを設定してください。';
}
