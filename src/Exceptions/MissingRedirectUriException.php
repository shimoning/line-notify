<?php

namespace Shimoning\LineNotify\Exceptions;

class MissingRedirectUriException extends LineNotifyException
{
    protected $message = '登録したリダイレクトURIを設定してください。';
}
