<?php

namespace TemporalKey\Tests;

use Carbon\Carbon;
use TemporalKey\Tests\Fixtures\ImagePreviewTmpKey;

class ExtendedTmpKeyTest extends TestCase
{
    /** @test */
    public function create_key_test()
    {
        $temporalKey = ImagePreviewTmpKey::create();

        $this->assertInstanceOf(ImagePreviewTmpKey::class, $temporalKey);
        $this->assertNull($temporalKey->meta());
        $this->assertEquals(1, \TemporalKey\TemporalKey::$storeModel::query()->count());

        /** @var \TemporalKey\Models\TemporalKeyStore $keyRow */
        $keyRow = \TemporalKey\TemporalKey::$storeModel::query()->latest()->first();

        $this->assertEquals($keyRow->key, $temporalKey->key());
        $this->assertEquals('image-preview', $keyRow->type);
        $this->assertEquals(2, $keyRow->usage_max);
        $this->assertEquals(0, $keyRow->usage_counter);
        $this->assertTrue($keyRow->valid_until->greaterThan(Carbon::now()->addMinutes(59)));
        $this->assertTrue($keyRow->valid_until->lessThan(Carbon::now()->addMinutes(61)));
    }

    /** @test */
    public function find_key_test()
    {
        $createdTemporalKey = ImagePreviewTmpKey::create();
        $temporalKey        = ImagePreviewTmpKey::find($createdTemporalKey->key());

        $this->assertIsString($temporalKey->key());
        $this->assertNull($temporalKey->meta());
        $this->assertEquals(1, \TemporalKey\TemporalKey::$storeModel::query()->count());

        $this->assertEquals($createdTemporalKey->key(), $temporalKey->key());

        // Totally retrieve 3 times
        $this->assertNotNull(ImagePreviewTmpKey::find($createdTemporalKey->key()));
        $this->assertNull(ImagePreviewTmpKey::find($createdTemporalKey->key()));
        $this->assertEquals(0, \TemporalKey\TemporalKey::$storeModel::query()->count());
    }

    /** @test */
    public function find_without_increment_key_test()
    {
        $createdTemporalKey = ImagePreviewTmpKey::create();
        $temporalKey        = ImagePreviewTmpKey::find($createdTemporalKey->key());

        $this->assertIsString($temporalKey->key());
        $this->assertNull($temporalKey->meta());
        $this->assertEquals(1, \TemporalKey\TemporalKey::$storeModel::query()->count());

        $this->assertEquals($createdTemporalKey->key(), $temporalKey->key());

        $this->assertNotNull(ImagePreviewTmpKey::findWithoutIncrement($createdTemporalKey->key()));
        $this->assertNotNull(ImagePreviewTmpKey::findWithoutIncrement($createdTemporalKey->key()));
        $this->assertNotNull(ImagePreviewTmpKey::findWithoutIncrement($createdTemporalKey->key()));
        $this->assertNotNull(ImagePreviewTmpKey::findWithoutIncrement($createdTemporalKey->key()));

        // Totally retrieve 3 times
        $this->assertNotNull(ImagePreviewTmpKey::find($createdTemporalKey->key()));
        $this->assertNull(ImagePreviewTmpKey::find($createdTemporalKey->key()));
        $this->assertNull(ImagePreviewTmpKey::findWithoutIncrement($createdTemporalKey->key()));
        $this->assertEquals(0, \TemporalKey\TemporalKey::$storeModel::query()->count());
    }
}
