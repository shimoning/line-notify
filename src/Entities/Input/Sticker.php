<?php

namespace Shimoning\LineNotify\Entities\Input;

/**
 * @see https://developers.line.biz/ja/docs/messaging-api/sticker-list/
 */
class Sticker
{
    private string $packageId;
    private string $id;

    public function __construct(string $packageId, string $id)
    {
        $this->packageId = $packageId;
        $this->id = $id;
    }

    public function getPackageId(): string
    {
        return $this->packageId;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
