<?php

namespace TemporalKey\Tests;

use Carbon\Carbon;

class TmpKeyTest extends TestCase
{
    /** @test */
    public function create_key_test()
    {
        $temporalKey = \TemporalKey\Manager\TmpKey::create();

        $this->assertIsString($temporalKey->key());
        $this->assertNull($temporalKey->meta());
        $this->assertEquals(1, \TemporalKey\TemporalKey::$storeModel::query()->count());

        /** @var \TemporalKey\Models\TemporalKeyStore $keyRow */
        $keyRow = \TemporalKey\TemporalKey::$storeModel::query()->latest()->first();

        $this->assertEquals($keyRow->key, $temporalKey->key());
        $this->assertEquals(0, $keyRow->usage_max);
        $this->assertEquals(0, $keyRow->usage_counter);
        $this->assertTrue($keyRow->valid_until->greaterThan(Carbon::now()));
    }

    /** @test */
    public function create_with_max_usage_key_test()
    {
        $date = Carbon::now()->addYear();

        $temporalKey = \TemporalKey\Manager\TmpKey::create(validUntil: $date, usageMax: 3);

        $this->assertIsString($temporalKey->key());
        $this->assertNull($temporalKey->meta());
        $this->assertEquals(1, \TemporalKey\TemporalKey::$storeModel::query()->count());

        /** @var \TemporalKey\Models\TemporalKeyStore $keyRow */
        $keyRow = \TemporalKey\TemporalKey::$storeModel::query()->latest()->first();

        $this->assertEquals($keyRow->key, $temporalKey->key());
        $this->assertEquals('default', $keyRow->type);
        $this->assertEquals(3, $keyRow->usage_max);
        $this->assertEquals(0, $keyRow->usage_counter);
        $this->assertEquals($date->format('Y-m-d H:i:s'), $keyRow->valid_until->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function create_with_meta_usage_key_test()
    {
        $temporalKey = \TemporalKey\Manager\TmpKey::create([
            'foo' => [
                'bar' => [
                    'baz' => 123,
                ],
            ],
        ]);

        $this->assertIsString($temporalKey->key());
        $this->assertIsArray($temporalKey->meta());
        $this->assertArrayHasKey('foo', $temporalKey->meta());
        $this->assertEquals(1, \TemporalKey\TemporalKey::$storeModel::query()->count());

        /** @var \TemporalKey\Models\TemporalKeyStore $keyRow */
        $keyRow = \TemporalKey\TemporalKey::$storeModel::query()->latest()->first();

        $this->assertEquals($keyRow->key, $temporalKey->key());
        $this->assertEquals(0, $keyRow->usage_max);
        $this->assertEquals(0, $keyRow->usage_counter);
        $this->assertIsArray($keyRow->meta);
        $this->assertEquals(123, $keyRow->meta['foo']['bar']['baz']);
    }

    /** @test */
    public function find_key_test()
    {
        $createdTemporalKey = \TemporalKey\Manager\TmpKey::create();
        $temporalKey        = \TemporalKey\Manager\TmpKey::find($createdTemporalKey->key());

        $this->assertIsString($temporalKey->key());
        $this->assertNull($temporalKey->meta());
        $this->assertEquals(1, \TemporalKey\TemporalKey::$storeModel::query()->count());

        $this->assertEquals($createdTemporalKey->key(), $temporalKey->key());

        // Totally retrieve 4 times
        \TemporalKey\Manager\TmpKey::find($createdTemporalKey->key());
        \TemporalKey\Manager\TmpKey::find($createdTemporalKey->key());
        \TemporalKey\Manager\TmpKey::find($createdTemporalKey->key());

        $keyRow = \TemporalKey\TemporalKey::$storeModel::query()->latest()->first();
        $this->assertEquals($keyRow->key, $temporalKey->key());
        $this->assertEquals(0, $keyRow->usage_max);
        $this->assertEquals(4, $keyRow->usage_counter);

        // Update valid date to set expired
        $keyRow->valid_until = Carbon::now()->subMinute();
        $keyRow->save();
        $this->assertNull(\TemporalKey\Manager\TmpKey::find($createdTemporalKey->key()));
        $this->assertEquals(0, \TemporalKey\TemporalKey::$storeModel::query()->count());
    }

    /** @test */
    public function find_key_with_max_usage_test()
    {
        $createdTemporalKey = \TemporalKey\Manager\TmpKey::create(usageMax: 3);
        $temporalKey        = \TemporalKey\Manager\TmpKey::find($createdTemporalKey->key());

        $this->assertIsString($temporalKey->key());
        $this->assertNull($temporalKey->meta());
        $this->assertEquals(1, \TemporalKey\TemporalKey::$storeModel::query()->count());

        $this->assertEquals($createdTemporalKey->key(), $temporalKey->key());

        // Totally retrieve 4 times
        $this->assertNotNull(\TemporalKey\Manager\TmpKey::find($createdTemporalKey->key()));
        $this->assertNotNull(\TemporalKey\Manager\TmpKey::find($createdTemporalKey->key()));
        $this->assertNull(\TemporalKey\Manager\TmpKey::find($createdTemporalKey->key()));
        $this->assertEquals(0, \TemporalKey\TemporalKey::$storeModel::query()->count());
    }
}
