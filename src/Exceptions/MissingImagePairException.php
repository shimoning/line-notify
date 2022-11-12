<?php

namespace Shimoning\LineNotify\Exceptions;

class MissingImagePairException extends LineNotifyException
{
    protected $message = 'サムネイルとフルサイズの両方の画像をセットしてください。';
}
