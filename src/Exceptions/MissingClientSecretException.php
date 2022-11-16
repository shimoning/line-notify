<?php

namespace Shimoning\LineNotify\Exceptions;

class MissingClientSecretException extends LineNotifyException
{
    protected $message = 'ClientSecretを設定してください。';
}
