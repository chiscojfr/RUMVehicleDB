<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('number');
            $table->string('name');
            $table->string('expiry')->nullable();
            $table->string('type');
            $table->string('status')->nullable();
            $table->string('cardID');
            $table->integer('auxiliary_custodian_id')->unsigned()->nullable();

            $table->integer('department_id')->unsigned();
            $table->integer('custodian_id')->unsigned();

            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments');

            $table->foreign('custodian_id')
                  ->references('id')
                  ->on('custodians');

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
        Schema::drop('cards');
    }
}
