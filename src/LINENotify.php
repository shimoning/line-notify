<?php

namespace Shimoning\LineNotify;

use Shimoning\LineNotify\Communicator\Api;
use Shimoning\LineNotify\Communicator\Auth;
use Shimoning\LineNotify\Entity\Output\Response;
use Shimoning\LineNotify\Entity\Output\AuthError;
use Shimoning\LineNotify\Entity\Output\AuthResult;
use Shimoning\LineNotify\Entity\Output\Status;
use Shimoning\LineNotify\ValueObject\Message;
use Shimoning\LineNotify\Constants\ResponseMode;

class LINENotify
{
    private string $clientId;
    private string $clientSecret;
    private $callbackUrl;
    public function __construct(
        string $clientId,
        string $clientSecret,
        string $callbackUrl,
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * 1. 認証用のURIを生成する
     *
     * @param string $state
     * @param ResponseMode|string|null|null $responseMode
     * @return string
     */
    public function generateAuthUri(
        string $state,
        ResponseMode|string|null $responseMode = null,
    ): string {
        return Auth::generateAuthUri(
            $this->clientId,
            $this->callbackUrl,
            $state,
            $responseMode,
        );
    }

    /**
     * 2. 認証結果をパース
     *
     * @param string|array $result
     * @return AuthResult|AuthError
     */
    public function parseAuthResult(string|array $result): AuthResult|AuthError
    {
        return Auth::parseAuthResult($result);
    }

    /**
     * 3. 認証コードを認証トークンに変換する
     *
     * @param string $code
     * @return string
     */
    public function exchangeCode4AccessToken(string $code): string
    {
        return Auth::exchangeCode4AccessToken(
            $this->clientId,
            $this->clientSecret,
            $this->callbackUrl,
            $code,
        );
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
        ?Image $image = null,
        ?Sticker $sticker = null,
        ?bool $notificationDisabled = false,
        ?bool $returnRawResponse = false,
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

    /**
     * 連携状態を確認する
     *
     * @param string $accessToken
     * @return Status|null
     */
    public function status(string $accessToken): ?Status
    {
        return Api::status($accessToken);
    }

    /**
     * 連携を解除する
     *
     * @param string $accessToken
     * @return bool
     */
    public function revokeAccessToken(string $accessToken): bool
    {
        return Api::revokeAccessToken($accessToken);
    }
}
