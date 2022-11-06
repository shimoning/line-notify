<?php

namespace Shimoning\LineNotify;

use GuzzleHttp\Client;

/**
 * TODO: implement
 *
 * @see https://notify-bot.line.me/doc/ja/
 */
class Auth
{
    /**
     * 認証用のURLを生成する
     * GET
     * https: //notify-bot.line.me/oauth/authorize
     * @return string $url
     */
    static public function generateAuthUrl()
    {
        $parameters = [
            'response_type' => 'code', // fixed
            'client_id' => '', // required
            'redirect_url' => '', // required
            'scope' => 'notify', // fixed
            'state' => '', // required
            'response_mode' => 'from_post', // option
        ];
    }

    /**
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

    // utilities

    /**
     * state を生成する
     * (セキュリティ上独自に作るのが望ましいが)
     *
     * @param string $identity
     * @return string
     */
    static public function generateState(string $identity) {}
}
