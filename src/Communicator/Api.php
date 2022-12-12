<?php

namespace Shimoning\LineNotify\Communicator;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Shimoning\LineNotify\Entity\Output\Response;
use Shimoning\LineNotify\Entity\Output\Status;
use Shimoning\LineNotify\Entity\Input\Image;
use Shimoning\LineNotify\Entity\Input\Sticker;
use Shimoning\LineNotify\ValueObject\Message;
use Shimoning\LineNotify\Exceptions\UnauthorizedException;

/**
 * @see https://notify-bot.line.me/doc/ja/
 */
class Api
{
    /**
     * 通知する
     *
     * @param string $accessToken
     * @param Message $message
     * @param Sticker|null $sticker
     * @param Image|null $image
     * @param bool $notificationDisabled (default = false)
     * @param bool $returnRawResponse (default = false)
     * @return Response|bool
     */
    public static function notify(
        string $accessToken,
        Message $message,
        ?Sticker $sticker = null,
        ?Image $image = null,
        ?bool $notificationDisabled = false,
        ?bool $returnRawResponse = false,
    ): Response|bool {
        $parameters = [
            [
                'name' => 'message',
                'contents' => $message->getValue(),
            ],
            [
                'name' => 'notificationDisabled',
                'contents' => $notificationDisabled ? 1 : 0,
            ],
        ];

        // sticker
        if ($sticker) {
            $parameters[] = [
                'name' => 'stickerPackageId',
                'contents' => $sticker->getPackageId(),
            ];
            $parameters[] = [
                'name' => 'stickerId',
                'contents' => $sticker->getId(),
            ];
        }

        // image:uri
        if ($image?->hasUri()) {
            $parameters[] = [
                'name' => 'imageThumbnail',
                'contents' => $image->getThumbnailUri(),
            ];
            $parameters[] = [
                'name' => 'imageFullsize',
                'contents' => $image->getFullSizeUri(),
            ];
        }

        // image:file
        if ($image?->hasFile()) {
            $parameters[] = [
                'name' => 'imageFile',
                'contents' => $image->getBinaryFile(),
                'filename' => $image->getFilename(),
            ];
        }

        $options = [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
            RequestOptions::MULTIPART => $parameters,
        ];

        $response = new Response(
            (new Client)->post('https://notify-api.line.me/api/notify', $options),
        );
        if ($returnRawResponse) {
            return $response;
        }

        if ($response->getHTTPStatus() === 401) {
            throw new UnauthorizedException('token の有効期限が切れています。');
        }

        return $response->isSucceeded();
    }

    /**
     * 連携状態を確認する
     *
     * @return Status|null
     */
    public static function status(string $accessToken): Status|null
    {
        $options = [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ];

        $response = new Response(
            (new Client)->get('https://notify-api.line.me/api/status', $options),
        );
        if ($response->isSucceeded()) {
            $result = $response->getJSONDecodedBody();
            return new Status($result['targetType'], $result['target']);
        }
        return null;
    }

    /**
     * 連携を解除する
     *
     * @param string $accessToken
     * @param bool $returnRawResponse (default = false)
     * @return Response|bool
     */
    public static function revokeAccessToken(string $accessToken, ?bool $returnRawResponse = false): Response|bool
    {
        $options = [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ];
        $response = new Response(
            (new Client)->post('https://notify-api.line.me/api/revoke', $options),
        );
        if ($returnRawResponse) {
            return $response;
        }

        if ($response->isSucceeded() || $response->getHTTPStatus() === 401) {
            return true;
        }

        return false;
    }
}
