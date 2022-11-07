<?php

namespace TemporalKey\Tests\Console;

use Carbon\Carbon;
use TemporalKey\Manager\TmpKey;
use TemporalKey\Tests\Fixtures\ImagePreviewTmpKey;
use TemporalKey\Tests\TestCase;

class PruneTemporalKeysCommandTest extends TestCase
{
    /** @test */
    public function nothing_to_delete()
    {
        $this->assertEquals(0, \TemporalKey\TemporalKey::$storeModel::query()->count());

        $this->artisan('temporal-key:prune')->assertExitCode(0);

        $this->assertEquals(0, \TemporalKey\TemporalKey::$storeModel::query()->count());
    }

    /** @test */
    public function all_valid()
    {
        TmpKey::create();
        TmpKey::create();
        ImagePreviewTmpKey::create();
        ImagePreviewTmpKey::create();

        $this->assertEquals(4, \TemporalKey\TemporalKey::$storeModel::query()->count());

        $this->artisan('temporal-key:prune')->assertExitCode(0);

        $this->assertEquals(4, \TemporalKey\TemporalKey::$storeModel::query()->count());
    }

    /** @test */
    public function delete_expired()
    {
        TmpKey::create();
        TmpKey::create(validUntil: Carbon::now()->subMinute());
        $usageKey = TmpKey::create(usageMax: 1);
        \TemporalKey\TemporalKey::$storeModel::query()->where('key', $usageKey->key())->update([
            'usage_counter' => 2,
        ]);
        ImagePreviewTmpKey::create();
        ImagePreviewTmpKey::create(validUntil: Carbon::now()->subMinute());
        $usageKey = ImagePreviewTmpKey::create(usageMax: 1);
        \TemporalKey\TemporalKey::$storeModel::query()->where('key', $usageKey->key())->update([
            'usage_counter' => 2,
        ]);

        $this->assertEquals(6, \TemporalKey\TemporalKey::$storeModel::query()->count());

        $this->artisan('temporal-key:prune')->assertExitCode(0);

        $this->assertEquals(2, \TemporalKey\TemporalKey::$storeModel::query()->count());
    }
}
