<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExcelNoReconciliateRecord extends Model
{
    protected $table = 'excel_no_reconciliate_records';

    protected $fillable = [
        'fecha_de_la_transaccion','ubicacion_de_compra', 'numero_de_transaccion', 'nombre_de_la_tarjeta', 'pieza', 'cantidad_litros', 'total_del_solicitante'
    ];
}
