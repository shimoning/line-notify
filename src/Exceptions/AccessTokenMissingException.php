<?php

namespace Shimoning\LineNotify\Exceptions;

class AccessTokenMissingException extends LineNotifyException
{
    protected $message = 'アクセストークンを設定してください。';
}
