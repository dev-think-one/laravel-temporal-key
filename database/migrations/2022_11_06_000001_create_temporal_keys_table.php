<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('temporal-key.tables.temporal_keys'), function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->index();
            $table->string('type', 100)->default('default')->index();
            $table->dateTime('valid_until')->index();
            $table->unsignedInteger('usage_counter')->default(0)->index();
            $table->unsignedInteger('usage_max')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['key', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('temporal-key.tables.temporal_keys'));
    }
};
