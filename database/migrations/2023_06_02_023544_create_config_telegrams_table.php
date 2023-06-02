<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigTelegramsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config_telegrams', function (Blueprint $table) {
            $table->id();
            $table->string('token_a', 100);
            $table->string('chat_id_a', 50)->nullable();
            $table->string('token_b', 100)->nullable();
            $table->string('chat_id_b', 50)->nullable();
            $table->text('admins')->nullable();
            $table->text('topic')->nullable();
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
        Schema::dropIfExists('config_telegrams');
    }
}
