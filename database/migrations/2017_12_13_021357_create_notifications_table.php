<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('custodian_id')->unsigned();
            $table->integer('record_id')->unsigned()->nullable();
            $table->integer('notification_type_id')->unsigned();
            $table->boolean('was_read');
            $table->string('justification')->nullable();
            $table->boolean('was_justified');
            $table->boolean('was_archived');
            $table->integer('status_type_id')->unsigned();
            //$table->string('record_not_found_info')->nullable();
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
        Schema::drop('notifications');
    }
}
