<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('vin')->nullable();
            $table->string('color')->nullable();
            $table->string('year')->nullable();
            $table->string('type')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('property_number')->nullable();
            $table->date('marbete_date')->nullable();
            $table->date('inspection_date')->nullable();
            $table->date('decomission_date')->nullable();
            $table->string('registration_id')->nullable();
            $table->string('title_id')->nullable();
            $table->integer('doors')->nullable();
            $table->integer('cylinders')->nullable();
            $table->string('ACAA')->nullable();
            $table->string('insurance')->nullable();
            $table->string('purchase_price')->nullable();
            $table->date('inscription_date')->nullable();
            $table->date('license_plate')->nullable();

            //Car Picture Attr.
            $table->string('filename')->nullable();
            $table->string('mime')->nullable();
            $table->string('original_filename')->nullable();

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
        Schema::drop('vehicles');
    }
}
