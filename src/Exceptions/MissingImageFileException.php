<?php

namespace Shimoning\LineNotify\Exceptions;

class MissingImageFileException extends LineNotifyException
{
    protected $message = '画像ファイルが見つかりません。';
}
