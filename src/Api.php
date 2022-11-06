<?php

namespace Shimoning\LineNotify;

use GuzzleHttp\Client;
use Shimoning\LineNotify\Exceptions\UnauthorizedException;

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
     * @return bool
     */
    public static function notify(
        string $accessToken,
        string $message,
    ): bool {
        $parameters = [
            'message' => $message, // required
            // 'imageThumbnail' => '', // option (jpeg)
            // 'imageFullsize' => '', // option (jpeg)
            // 'imageFile' => '', // option (png, jpeg)
            // 'stickerPackageId' => -1, // option @see
            // 'stickerId' => -1, // option @see
            // 'notificationDisabled' => false, // option
        ];

        $response = (new Client)->post('https://notify-api.line.me/api/notify', [
            'http_errors' => false,
            'headers' => [
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Authorization' => 'Bearer ' . $accessToken,
            ],
            'form_params' => $parameters, // if having imageFile: 'multipart' => []
        ]);
        $statusCode = $response->getStatusCode();
        return 200 <= $statusCode && $statusCode < 300;
    }

    /**
     * TODO: implement
     *
     * 連携状態を確認する
     * GET
     * https: //notify-bot.line.me/api/status
     *
     * @return Status
     */
    public static function status(string $accessToken)
    {
        // new Status(string $message, TargetType $targetType, string $target)
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
