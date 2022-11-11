<?php

namespace Shimoning\LineNotify;

use Shimoning\LineNotify\Entities\Response;

class LINENotify
{
    private string $accessToken;
    private bool $returnRawResponse = false;

    /**
     * @param string $accessToken
     */
    public function __construct(string $accessToken, bool $returnRawResponse = false)
    {
        $this->accessToken = $accessToken;
        $this->returnRawResponse = $returnRawResponse;
    }

    /**
     * 通知する
     *
     * @param string $message
     * @param Image|null $image
     * @param Sticker|null $sticker
     * @param bool $notificationDisabled (default = false)
     * @return Response|bool
     */
    public function notify(
        string $message,
        $image = null,
        $sticker = null,
        $notificationDisabled = false,
    ): Response|bool {
        return Api::notify(
            $this->accessToken,
            $message,
            $image,
            $sticker,
            $notificationDisabled,
            $this->returnRawResponse,
        );
    }
}
