<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleUsageRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_usage_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('receipt_number');
            $table->date('date');
            $table->string('provider_number');
            $table->string('purchase_type');
            $table->string('total_liters');
            $table->string('total_receipt');
            $table->string('vehicle_mileage')->nullable();
            $table->string('comments')->nullable();

            $table->string('filename')->nullable();
            $table->string('mime')->nullable();
            $table->string('original_filename')->nullable();

            $table->integer('vehicle_id')->unsigned();
            $table->integer('card_id')->unsigned();
            $table->integer('custodian_id')->unsigned();

            $table->foreign('vehicle_id')
                  ->references('id')
                  ->on('vehicles');

            $table->foreign('card_id')
                  ->references('id')
                  ->on('cards');

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
        Schema::drop('vehicle_usage_records');
    }
}
