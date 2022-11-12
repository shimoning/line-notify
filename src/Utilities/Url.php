<?php

namespace Shimoning\LineNotify\Utilities;

class Url
{
    /**
     * URL を生成
     *
     * @param string $baseUrl
     * @param string $path
     * @param array $query
     * @return string
     */
    public static function generate(
        string $baseUrl,
        string $path = '',
        array $query = []
    ): string {
        $normalizedPath = !empty($path)
            ? '/' . \ltrim($path, '/')
            : '';
        $queryString = !empty($query)
            ? '?' . \http_build_query($query)
            : '';
        return \rtrim($baseUrl, '/') . $normalizedPath . $queryString;
    }
}
