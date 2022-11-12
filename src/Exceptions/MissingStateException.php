<?php

namespace Shimoning\LineNotify\Exceptions;

class MissingStateException extends LineNotifyException
{
    protected $message = 'CSRF 攻撃に対応するための任意のトークンを設定してください。';
}
