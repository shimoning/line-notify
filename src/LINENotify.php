<?php

namespace Shimoning\LineNotify;

class LINENotify
{
    private string $accessToken;

    /**
     * @param string $accessToken
     */
    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * 通知する
     *
     * @param string $message
     * @param Image|null $image
     * @param Sticker|null $sticker
     * @param bool $notificationDisabled (default = false)
     * @return bool
     */
    public function notify(string $message): bool
    {
        return Api::notify($this->accessToken, $message);
    }
}
