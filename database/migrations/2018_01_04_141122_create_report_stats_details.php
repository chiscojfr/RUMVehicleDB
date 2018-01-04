<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportStatsDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_stats_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('conciliation_dates')->nullable();
            $table->string('formatted_conciliation_dates')->nullable();
            $table->string('conciliation_percent')->nullable();
            $table->string('total_excel_records')->nullable();
            $table->string('total_server_records')->nullable();
            $table->string('total_expenses_in_excel_records')->nullable();
            $table->string('total_expenses_in_server_records')->nullable();
            $table->string('after_conciliation_percent')->nullable();

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
        Schema::drop('report_stats_details');
    }
}
