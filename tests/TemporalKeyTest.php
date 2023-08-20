<?php

namespace TemporalKey\Tests;

use Illuminate\Foundation\Auth\User;
use TemporalKey\Models\TemporalKeyStore;
use TemporalKey\TemporalKey;

class TemporalKeyTest extends TestCase
{

    /** @test */
    public function ignore_migrations()
    {
        $this->assertTrue(TemporalKey::$runsMigrations);

        TemporalKey::ignoreMigrations();

        $this->assertFalse(TemporalKey::$runsMigrations);

        TemporalKey::$runsMigrations = true;
    }

    /** @test */
    public function use_model()
    {
        $this->assertEquals(TemporalKeyStore::class, TemporalKey::$storeModel);

        TemporalKey::useModel(User::class);

        $this->assertEquals(User::class, TemporalKey::$storeModel);

        TemporalKey::useModel(TemporalKeyStore::class);
    }
}
