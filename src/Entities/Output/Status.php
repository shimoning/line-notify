<?php

namespace Shimoning\LineNotify\Entities\Output;

use Shimoning\LineNotify\Constants\TargetType;
use Shimoning\LineNotify\Exceptions\LineNotifyException;

class Status
{
    private TargetType $targetType;
    private ?string $target;

    public function __construct(string|TargetType $targetType, ?string $target)
    {
        $normalizedTargetType = \is_string($targetType)
            ? TargetType::tryFrom($targetType)
            : $targetType;
        if (! ($normalizedTargetType instanceof TargetType)) {
            throw new LineNotifyException('target_type の値が異常です。');
        }
        $this->targetType = $normalizedTargetType;
        $this->target = $target;
    }

    public function getTargetType(): TargetType
    {
        return $this->targetType;
    }

    public function getTargetTypeValue(): string
    {
        return $this->targetType->value;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }
}
