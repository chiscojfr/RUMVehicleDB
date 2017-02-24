<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustodiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custodians', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('position')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('employee_id')->nullable();

            $table->integer('user_type_id')->unsigned();
            $table->integer('department_id')->unsigned();

            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments');

            $table->foreign('user_type_id')
                  ->references('id')
                  ->on('user_types');

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
        Schema::drop('custodians');
    }
}
