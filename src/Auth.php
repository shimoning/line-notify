<?php

namespace Shimoning\LineNotify;

use GuzzleHttp\Client;
use Shimoning\LineNotify\Constants\ResponseMode;
use Shimoning\LineNotify\Exceptions\ValidationException;
use Shimoning\LineNotify\Utilities\Url;

/**
 * @see https://notify-bot.line.me/doc/ja/
 */
class Auth
{
    /**
     * 認証用のURIを生成する
     * https://notify-bot.line.me/oauth/authorize
     * @return string $uri
     */
    static public function generateAuthUri(
        string $clientId,
        string $redirectUri,
        string $state,
        ResponseMode|string|null $responseMode = null,
    ): string {
        $parameters = [
            'response_type' => 'code', // fixed
            'client_id'     => $clientId, // required
            'redirect_uri'  => $redirectUri, // required
            'state'         => $state, // required
            'scope'         => 'notify', // fixed
        ];

        if (!empty($responseMode)) {
            $normalizedResponseMode = \is_string($responseMode)
                ? ResponseMode::tryFrom($responseMode)
                : $responseMode;
            \var_dump($normalizedResponseMode, $responseMode);
            if (! ($normalizedResponseMode instanceof ResponseMode)) {
                throw new ValidationException('response-mode は未入力にするか、 "form_post" を入力してください。');
            }
            $parameters['response_mode'] = $normalizedResponseMode->value;
        }

        return Url::generate('https://notify-bot.line.me/oauth/authorize', '', $parameters);
    }

    /**
     * TODO: implement
     *
     * 認証結果をパース
     *
     * @return AuthResult
     */
    static public function parseAuthResult()
    {
        // Succeed: code, state
        // Failed: error, error_description
    }

    /**
     * トークン認証をアクセスコードに転換する
     * POST
     * https: //notify-bot.line.me/oauth/token
     *
     * @return string $access_code
     */
    static public function token()
    {
        $parameters = [
            'grant_type' => 'authorization_code', // fixed
            'code' => '', // required
            'redirect_uri' => '', // required : need to same request
            'client_id' => '', // required
            'client_secret' => '', // required
        ];
    }

    /**
     * state を簡易生成する
     * (セキュリティ上、独自に作るのが望ましい)
     *
     * @param string $identity
     * @return string
     */
    static public function generateState(string $identity): string
    {
        return \md5(\uniqid() . $identity);
    }
}
