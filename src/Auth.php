<?php

namespace Shimoning\LineNotify;

use GuzzleHttp\Client;
use Shimoning\LineNotify\Constants\ResponseMode;
use Shimoning\LineNotify\Entities\Output\Response;
use Shimoning\LineNotify\Entities\Output\AuthResult;
use Shimoning\LineNotify\Entities\Output\AuthError;
use Shimoning\LineNotify\Exceptions\UnauthorizedException;
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
     *
     * @param string $clientId
     * @param string $redirectUri
     * @param string $state  CSRF対策トークンなどを指定する。特に response_mode=form_post の場合。
     * @param ResponseMode|string|null $responseMode
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
            if (! ($normalizedResponseMode instanceof ResponseMode)) {
                throw new ValidationException('response-mode は未入力にするか、 "form_post" を入力してください。');
            }
            $parameters['response_mode'] = $normalizedResponseMode->value;
        }

        return Url::generate('https://notify-bot.line.me/oauth/authorize', '', $parameters);
    }

    /**
     * 認証結果をパース
     *
     * @param string $queryString
     * @return AuthResult|AuthError
     */
    static public function parseAuthResult(string $queryString)
    {
        $query = [];
        \parse_str($queryString, $query);

        return isset($query['code'])
            ? new AuthResult($query['code'], $query['state'] ?? '')
            : new AuthError($query['error'] ?? '', $query['error_description'] ?? '');
    }

    /**
     * トークン認証をアクセスコードに転換する
     * https://notify-bot.line.me/oauth/token
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @param string $code
     * @param bool $returnRawResponse (default = false)
     * @return Response|string|null
     */
    static public function token(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        string $code,
        ?bool $returnRawResponse = false,
    ): Response|string|null {
        $options = [
            'http_errors' => false,
            'form_params' => [
                'grant_type'    => 'authorization_code', // fixed
                'code'          => $code, // required
                'client_id'     => $clientId, // required
                'client_secret' => $clientSecret, // required
                'redirect_uri'  => $redirectUri, // required : need to same request
            ],
        ];

        $response = new Response(
            (new Client)->post('https://notify-bot.line.me/oauth/token', $options),
        );
        if ($returnRawResponse) {
            return $response;
        }
        if ($response->isSucceeded()) {
            $result = $response->getJSONDecodedBody();
            return $result['access_token'];
        }

        if ($response->getHTTPStatus() === 401) {
            throw new UnauthorizedException('code の有効期限が切れています。');
        }

        return null;
    }

    /**
     * state を簡易生成する
     *
     * セキュリティ上、独自に作るのが望ましい
     * 特に response_mode=form_post の場合は、 CSRF トークンなどを指定する。
     *
     * @param string $identity
     * @return string
     */
    static public function generateState(string $identity): string
    {
        return \md5(\uniqid() . $identity);
    }
}
