<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportExcelNoReconciliatedRecord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_excel_no_reconciliate_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cliente')->nullable();
            $table->string('id_de_transaccion')->nullable();
            $table->string('fecha_de_la_transaccion')->nullable();
            $table->string('ubicacion_de_compra')->nullable();
            $table->string('numero_de_transaccion')->nullable();
            $table->string('nombre_de_la_tarjeta')->nullable();
            $table->string('pieza')->nullable();
            $table->string('cantidad_litros')->nullable();
            $table->string('total_del_solicitante')->nullable();
            $table->string('ultimos_4_tarjeta')->nullable();

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
        Schema::drop('report_excel_no_reconciliate_records');
    }
}
