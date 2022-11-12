<?php

namespace Shimoning\LineNotify\Exceptions;

class MissingClientIdException extends LineNotifyException
{
    protected $message = 'ClientIdを設定してください。';
}
