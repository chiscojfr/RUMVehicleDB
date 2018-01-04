<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportStatsDetails extends Model
{
    protected $table = 'report_stats_details';

    protected $fillable = [
        'formatted_conciliation_dates','conciliation_percent','conciliation_date_from','conciliation_date_to','total_excel_records','total_server_records', 'total_expenses_in_excel_records', 'total_expenses_in_server_records', 'after_conciliation_percent'
    ];
}
