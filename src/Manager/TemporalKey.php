<?php

namespace TemporalKey\Manager;

use Carbon\Carbon;

interface TemporalKey
{
    public static function find(string $key): ?static;

    public static function findWithoutIncrement(string $key): ?static;

    public static function create(?array $meta = null, ?Carbon $validUntil = null, ?int $usageMax = null): static;

    public function key(): string;

    public function meta(): ?array;
}
