<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleNoReconciledRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_no_reconciled_records', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('vehicle_usage_record_id')->unsigned();

            $table->foreign('vehicle_usage_record_id')
                  ->references('id')
                  ->on('vehicle_usage_records');

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
        Schema::drop('vehicle_no_reconciled_records');
    }
}
