<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vccs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('card_id', 255)->unique();
            $table->string('card_number', 100)->nullable();
            $table->decimal('balance', 5, 2)->nullable();
            $table->string('cvv', 5)->nullable();
            $table->date('expiration_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vccs');
    }
};
