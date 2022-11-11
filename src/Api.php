<?php

namespace Shimoning\LineNotify;

use GuzzleHttp\Client;
use Shimoning\LineNotify\Entities\Input\Image;
use Shimoning\LineNotify\Entities\Input\Sticker;
use Shimoning\LineNotify\Entities\Output\Response;
use Shimoning\LineNotify\Entities\Output\Status;

/**
 * @see https://notify-bot.line.me/doc/ja/
 */
class Api
{
    /**
     * 通知する
     *
     * @see https: //notify-bot.line.me/api/notify
     *
     * @param string $accessToken
     * @param string $message
     * @param Image|null $image
     * @param Sticker|null $sticker
     * @param bool $notificationDisabled (default = false)
     * @param bool $returnRawResponse (default = false)
     * @return Response|bool
     */
    public static function notify(
        string $accessToken,
        string $message,
        ?Image $image = null,
        ?Sticker $sticker = null,
        ?bool $notificationDisabled = false,
        ?bool $returnRawResponse = false,
    ): Response|bool {
        $parameters = [
            'message' => $message,
            'notificationDisabled' => $notificationDisabled,
        ];

        // image
        if ($image?->hasImage()) {
            $parameters['imageThumbnail'] = $image->getThumbnail();
            $parameters['imageFullsize'] = $image->getFullSize();
        }

        // sticker
        if ($sticker) {
            $parameters['stickerPackageId'] = $sticker->getPackageId();
            $parameters['stickerId'] = $sticker->getId();
        }

        $options = [
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ];
        if ($image?->hasFile()) {
            $_parameters = [];
            foreach ($parameters as $key => $value) {
                $_parameters[] = [
                    'name' => $key,
                    'contents' => $value,
                ];
            }
            $_parameters[] = [
                'name' => 'imageFile',
                'contents' => $image->getBinaryFile(),
            ];
            $options['multipart'] = $_parameters;
        } else {
            $options['form_params'] = $parameters;
        }

        $response = new Response(
            (new Client)->post('https://notify-api.line.me/api/notify', $options),
        );
        if ($returnRawResponse) {
            return $response;
        }

        return $response->isSucceeded();
    }

    /**
     * 連携状態を確認する
     * https: //notify-bot.line.me/api/status
     *
     * @return Status|false
     */
    public static function status(string $accessToken): Status|bool
    {
        $options = [
            'http_errors' => false,
            'headers' => [
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
        return false;
    }

    /**
     * TODO: implement
     *
     * 連携を解除する
     * POST
     * https: //notify-bot.line.me/api/revoke
     *
     * @return string $access_code
     */
    public static function revoke(string $accessToken)
    {
        // statusCode = 200: 解除成功
        // statusCode = 401: アクセストークンが既に無効になっている
        // else: 異常状態, retry が推奨される
    }
}
