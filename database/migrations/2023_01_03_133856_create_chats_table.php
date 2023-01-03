<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sender')->unsigned();
            $table->foreign('sender')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->integer('recipient')->unsigned();
            $table->foreign('recipient')
                    ->references('id')
                    ->on('users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->string('message')->nullable();
            $table->boolean('status');
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
        Schema::dropIfExists('chats');
    }
}
