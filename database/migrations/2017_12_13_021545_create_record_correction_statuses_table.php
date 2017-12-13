<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordCorrectionStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('record_correction_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('custodian_id')->unsigned();
            $table->integer('record_id')->unsigned()->nullable();
            $table->integer('status_type_id')->unsigned();
            $table->string('record_not_found_info')->nullable();
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
        Schema::drop('record_correction_statuses');
    }
}
