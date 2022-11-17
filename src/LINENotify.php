<?php

namespace Shimoning\LineNotify;

use Shimoning\LineNotify\Communicator\Api;
use Shimoning\LineNotify\Entity\Output\Response;
use Shimoning\LineNotify\ValueObject\Message;

class LINENotify
{
    private string $channelId;
    private string $channelSecret;
    private $callbackUrl;
    public function __construct(
        string $channelId,
        string $channelSecret,
        string $callbackUrl,
    ) {
        $this->channelId = $channelId;
        $this->channelSecret = $channelSecret;
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * 通知する
     *
     * @param string $accessToken
     * @param Message $message
     * @param Image|null $image
     * @param Sticker|null $sticker
     * @param bool $notificationDisabled (default = false)
     * @param bool $returnRawResponse (default = false)
     * @return Response|bool
     */
    public function notify(
        string $accessToken,
        Message $message,
        $image = null,
        $sticker = null,
        $notificationDisabled = false,
        $returnRawResponse = false,
    ): Response|bool {
        return Api::notify(
            $accessToken,
            $message,
            $image,
            $sticker,
            $notificationDisabled,
            $returnRawResponse,
        );
    }
}
