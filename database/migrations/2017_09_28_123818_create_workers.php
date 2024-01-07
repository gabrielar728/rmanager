<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkers extends Migration
{
    /**
     * Run the migrations. utf8mb4_unicode_ci
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('last');
            $table->string('first');
            $table->string('card')->unique();
            $table->boolean('status')->default(1);
            $table->string('password')->default('$2y$10$/twBl/dPKo38iSPP2uZW4ObViTLmooLhryhE4KphnGG2BEAty2PmK');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**d
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workers');
    }
}
