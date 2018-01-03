<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportExcelNoReconciliateRecord extends Model
{
    protected $table = 'report_excel_no_reconciliate_records';

    protected $fillable = [
        'id_de_transaccion','cliente','fecha_de_la_transaccion','ubicacion_de_compra', 'numero_de_transaccion', 'nombre_de_la_tarjeta', 'pieza', 'cantidad_litros', 'total_del_solicitante'
    ];
}
