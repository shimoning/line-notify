<?php

namespace Shimoning\LineNotify\Entity\Output;

use Psr\Http\Message\ResponseInterface;

class Response
{
    private ResponseInterface $response;

    private int $httpStatus;
    private string $body;
    /** @var string[] */
    private array $headers;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;

        $this->httpStatus = $response->getStatusCode();
        $this->body = $response->getBody()->getContents();
        $this->headers = $response->getHeaders();
    }

    /**
     * HTTP ステータスを取得する
     *
     * @return int
     */
    public function getHTTPStatus(): int
    {
        return $this->httpStatus;
    }

    /**
     * リクエストが成功かどうか
     *
     * @return boolean
     */
    public function isSucceeded(): bool
    {
        return 200 <= $this->httpStatus && $this->httpStatus <= 299;
    }

    /**
     * 取得した Body をそのまま取得する
     *
     * @return string
     */
    public function getRawBody(): string
    {
        return $this->body;
    }

    /**
     * JSON をパースされた Body を取得する
     *
     * @return array
     */
    public function getJSONDecodedBody(): array
    {
        return \json_decode($this->body, true);
    }

    /**
     * レスポンスヘッダを取得する
     *
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * 指定のレスポンスヘッダを取得する
     *
     * @param string $name
     * @return string|null
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    /**
     * 取得した Response をそのまま取得する
     *
     * @return string
     */
    public function getRaw(): ResponseInterface
    {
        return $this->response;
    }
}
