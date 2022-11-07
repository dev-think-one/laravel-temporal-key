<?php

namespace TemporalKey\Manager;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TmpKey implements TemporalKey
{
    protected string $key;
    protected ?array $meta = null;

    public static string $type             = 'default';
    public static int $defaultValidSeconds = 60;
    public static int $defaultUsageMax     = 0;

    protected function __construct(string $key, ?array $meta = null)
    {
        $this->key  = $key;
        $this->meta = $meta;
    }

    protected static function findModel(string $key): ?Model
    {
        $temporalKey = \TemporalKey\TemporalKey::$storeModel::query()
            ->where('key', $key)
            ->where('type', static::$type)
            ->first();

        if (!$temporalKey) {
            return null;
        }

        if (
            !$temporalKey->valid_until?->gt(Carbon::now())
            || (
                $temporalKey->usage_max
                && $temporalKey->usage_max <= $temporalKey->usage_counter
            )
        ) {
            $temporalKey->delete();

            return null;
        }

        return $temporalKey;
    }


    public static function findWithoutIncrement(string $key): ?static
    {
        $temporalKey = static::findModel($key);
        if ($temporalKey) {
            return new static($temporalKey->key, $temporalKey->meta);
        }

        return null;
    }


    public static function find(string $key): ?static
    {
        $temporalKey = static::findModel($key);
        if ($temporalKey) {
            $temporalKey->increment('usage_counter');

            return new static($temporalKey->key, $temporalKey->meta);
        }

        return null;
    }

    public static function create(?array $meta = null, ?Carbon $validUntil = null, ?int $usageMax = null): static
    {
        $temporalKey = \TemporalKey\TemporalKey::$storeModel::create([
            'key'         => static::generateKey(),
            'type'        => static::$type,
            'valid_until' => $validUntil ?? Carbon::now()->addSeconds(static::defaultValidSeconds()),
            'usage_max'   => $usageMax   ?? static::defaultUsageMax(),
            'meta'        => $meta,
        ]);

        return new static($temporalKey->key, $temporalKey->meta);
    }

    protected static function defaultValidSeconds(): int
    {
        return static::$defaultValidSeconds;
    }

    protected static function defaultUsageMax(): int
    {
        return static::$defaultUsageMax;
    }

    protected static function generateKey(): string
    {
        return Str::uuid()->toString();
    }

    public function key(): string
    {
        return $this->key;
    }

    public function meta(): ?array
    {
        return $this->meta;
    }
}
