<?php

namespace Shimoning\LineNotify\Exceptions;

class ImagePairMissingException extends LineNotifyException
{
    protected $message = 'サムネイルとフルサイズの両方の画像をセットしてください。';
}
