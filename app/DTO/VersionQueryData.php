<?php

namespace App\DTO;

use Carbon\Carbon;

readonly class VersionQueryData
{
    public function __construct(
        private int|string      $key,
        private null|int|string $timestamp
    )
    {
    }

    public function getKey(): int|string
    {
        return $this->key;
    }

    public function hasTimestamp(): bool
    {
        return $this->timestamp !== null;
    }

    public function getCaronDateTimeFromTimestamp(): Carbon
    {
        return Carbon::createFromTimestampUTC($this->timestamp);
    }
}