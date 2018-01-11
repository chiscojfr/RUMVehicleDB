<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportNoConciliatedRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_no_conciliated_records', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('vehicle_usage_record_id')->unsigned();

            $table->date('record_date')->nullable();

            $table->string('comments')->nullable();
            
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
        Schema::drop('report_no_conciliated_records');
    }
}
