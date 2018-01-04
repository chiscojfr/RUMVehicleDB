<?php

namespace App\Http\Controllers\v1;

use Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Custodian;
use App\UserType;
use App\Card;
use App\Vehicle;
use App\VehicleUsageRecord;
use App\VehicleReconciledRecord;
use App\Notification;
use App\Services\v1\CustodiansService;
use App\Services\v1\CardsService;
use App\Services\v1\VehiclesService;
use App\Services\v1\VehicleUsageRecordService;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Excel;
use Carbon\Carbon;
use App\Department;
use App\VehicleNoReconciledRecord;
use App\ExcelNoReconciliateRecord;

use App\ReportVehicleReconciledRecord;
use App\ReportVehicleNoReconciledRecord;
use App\ReportExcelNoReconciliateRecord;
use App\ReportStatsDetails;

class VehicleUsageRecordController extends Controller
{
    public function __construct(VehicleUsageRecordService $service){
        $this->records = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | Get record info depending user role.
    |--------------------------------------------------------------------------
    |
    | Param: Filter parameters
    | Return: record info
    |
    */
    public function index(Request $request)
    {
        $user = $this->records->getAuthenticatedUser();
        if($user->user_type_name == 'admin'){

            $data = $this->records->getRecords($request);

            return response()->json(['data' => $data], 200);
        }
        else {

             $data = $this->records->getUserRecords($user->id);
             if(empty($data)){
                return response()->json(['message' => 'Error: This custodian has not have records.'], 404);
             }
             return response()->json(['data' => $data], 200);
        }
        
    }

    /*
    |--------------------------------------------------------------------------
    | Create new record.
    |--------------------------------------------------------------------------
    |
    | Param: New record request
    | Return: New record info
    |
    */
    public function store(Request $request)
    {   
        $user = $this->records->getAuthenticatedUser();
        if($user->user_type_name == 'admin' || $user->user_type_name == 'custodian'){

            $record = $this->records->createRecord($request);

            if($record == null){
                return response()->json(['message' => 'ERROR: Please enter a file.'], 406);
            }
            else{
                return response()->json(['message' => 'Record created successfully!',$record], 201);
            }   

            
        }
        else {
            return response()->json(['message' => 'Error: Only Admin or Custodians can create new records.'], 401);
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Get single record info
    |--------------------------------------------------------------------------
    |
    | Param: record_id
    | Return: Record info
    |
    */
    public function show($id)
    {   
        $user = $this->records->getAuthenticatedUser();
        $data = $this->records->getRecordInfo($id);

        if($data == null ){
            return response()->json(['message' => 'Error: Record not found!'],404);
        }
        else if($user->user_type_name == 'admin' || $user->user_type_name == 'custodian'){
            return response()->json(['data' => $data], 200);
        }
        else{
            return response()->json(['message' => 'Error: Only Admin can view this info.'], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update record info 
    |--------------------------------------------------------------------------
    |
    | Param: Request, vehicle_id 
    | Reutrn: Updated vehicle info
    |
    */
    public function update(Request $request, $id)
    {   
        $user = $this->records->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){
            return $this->records->updateRecord($request, $id);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can update records.'], 401);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete record  
    |--------------------------------------------------------------------------
    |
    | Param: record_id 
    | Return: sucessful message
    |
    */
    public function destroy($id)
    {   
        $user = $this->records->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){
            return $this->records->deleteRecord($id);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can delete records.'], 401);
        }

    }
    
    /*
    |--------------------------------------------------------------------------
    | Get Record image  
    |--------------------------------------------------------------------------
    |
    | Param: filename 
    | Return: Record image
    |
    */
    public function get($filename)
    {
        $entry = VehicleUsageRecord::where('filename', '=', $filename)->firstOrFail();
        $file = Storage::disk('local')->get($entry->filename);
 
        return (new Response($file, 200))
              ->header('Content-Type', $entry->mime);
    }

    /*
    |--------------------------------------------------------------------------
    | Get records for an specific card 
    |--------------------------------------------------------------------------
    |
    | Param: card_id 
    | Return: Records for an specific card
    |
    */
    public function getCardRecords($id){
        return $this->records->getCardRecords($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Reconcile server records with Total Petrolleum Records  
    |--------------------------------------------------------------------------
    |
    | Param: .xls file 
    | Return: Reconcilation results
    |
    */
    public function reconcile(Request $request)
    {

       $file = Request::file('file');

       //Check if file are uploaded
       if($file == null){
         return response() -> json(['message' => 'ERROR: Please enter a .xls file.'], 404);
       }

       

       $original_file_name=$file->getClientOriginalName();
       $extension=$file->getClientOriginalExtension();
       
       //Check if extension is .xls 
       if($extension != 'xls'){
         return response() -> json(['message' => 'ERROR: Please enter a valid .xls file.'], 406);
       }

       $r1 = Storage::disk('local')->put($original_file_name, \File::get($file) );
       $route  =  storage_path('app') ."/". $original_file_name;
       
       //Fetch server records and compare data to excel file
       if($r1){
              
        $excel_records = Excel::selectSheetsByIndex(0)->load($route, function($sheet) {

                 $excel_records = $sheet->toArray();
                 $date_from = $excel_records[0]['fecha_de_la_transaccion'];
                 $date_to = $excel_records[0]['fecha_de_la_transaccion'];
             
                 //To find the date range in the excell record
                 foreach ($excel_records as $record){

                    if( $date_from->gt($record['fecha_de_la_transaccion'])){
                        $date_from = $record['fecha_de_la_transaccion'];
                    }
                    if( $date_to->lt($record['fecha_de_la_transaccion'])){
                        $date_to = $record['fecha_de_la_transaccion'];
                    }
                 }

                 //Query to find the server records who are inside in the date range of the excel record.
                 $server_records = VehicleUsageRecord::whereBetween('date', [$date_from->startOfDay(), $date_to->endOfDay()])->get()->toArray();

                 $reconcile_records = array();
                 $no_reconcile_records = $server_records;
                 $no_reconcile_records_excel = array();
                 $reconcile_records_count = 0;


                    //Concile excel records vs server records.
                    foreach ($excel_records as $excel_record){

                        foreach ($server_records as $server_record){

                           if( ($excel_record['fecha_de_la_transaccion']->toDateString() == $server_record['date']) &&
                               ($excel_record['numero_de_transaccion'] == $server_record['receipt_number']) &&
                               ($excel_record['cantidad'] == $server_record['total_liters']) &&
                               ($excel_record['total_del_solicitante'] == $server_record['total_receipt']) ){
                               
                               array_push($reconcile_records, $server_record);
                               $reconcile_records_count++;
                           }

                        }
                        if($reconcile_records_count == 0){
                            array_push($no_reconcile_records_excel, $excel_record);
                        }
                        else{
                            $reconcile_records_count = 0;
                        }
                    }

                    //Find No reconcile records in server records
                    if(!empty($reconcile_records)){

                        foreach ($reconcile_records as $reconcile_record){
                            
                            foreach ($server_records as $server_record){
                                
                                if($reconcile_record['id'] == $server_record['id']){
                                    $pos = array_search($server_record, $no_reconcile_records);
                                    unset($no_reconcile_records[$pos]);
                                }
                            }

                        }
                    }

                    //Delete temporary records
                    VehicleReconciledRecord::truncate();
                    VehicleNoReconciledRecord::truncate();
                    ExcelNoReconciliateRecord::truncate();

                    //Save temporary records
                    foreach ($reconcile_records as $reconcile_record){
                        $entry = new VehicleReconciledRecord();
                        $entry->vehicle_usage_record_id = $reconcile_record['id'];
                        $entry->comments = 'Record Concilied!';
                        $entry->save();
                    }

                    foreach ($no_reconcile_records as $no_reconcile_record){
                        $entry = new VehicleNoReconciledRecord();
                        $entry->vehicle_usage_record_id = $no_reconcile_record['id'];

                        if($no_reconcile_record['date'] == $date_to->toDateString()){

                            $entry->comments = '[Cutoff date!] Record stored in server but not reconciled!';
                        }
                        else{

                            $entry->comments = 'Record stored in server but not reconciled!';
                        }
                        $entry->save();
                    }

                    foreach ($no_reconcile_records_excel as $no_reconcile_record){
                        $entry = new ExcelNoReconciliateRecord();
                        $entry->cliente = $no_reconcile_record['cliente'];
                        $entry->fecha_de_la_transaccion = $no_reconcile_record['fecha_de_la_transaccion']->toDateString();
                        $entry->ubicacion_de_compra = $no_reconcile_record['ubicacion_de_compra'];
                        $entry->numero_de_transaccion = $no_reconcile_record['numero_de_transaccion'];
                        $entry->nombre_de_la_tarjeta = $no_reconcile_record['nombre_de_la_tarjeta'];
                        $entry->pieza = $no_reconcile_record['pieza'];
                        $entry->cantidad_litros = $no_reconcile_record['cantidad'];
                        $entry->total_del_solicitante = $no_reconcile_record['total_del_solicitante'];

                        $entry->save();
                    }

                    //Save records to reports tables
                    foreach ($reconcile_records as $reconcile_record){

                        if( ReportVehicleReconciledRecord::where('vehicle_usage_record_id', '=', $reconcile_record['id'])->count() == 0  ){
                            $entry = new ReportVehicleReconciledRecord();
                            $entry->vehicle_usage_record_id = $reconcile_record['id'];
                            $entry->comments = 'Record Concilied!';
                            $entry->save();
                        }
                    }

                    foreach ($no_reconcile_records as $no_reconcile_record){
                        if( ReportVehicleNoReconciledRecord::where('vehicle_usage_record_id', '=', $no_reconcile_record['id'])->count() == 0  ){
                            $entry = new ReportVehicleNoReconciledRecord();
                            $entry->vehicle_usage_record_id = $no_reconcile_record['id'];

                            if($no_reconcile_record['date'] == $date_to->toDateString()){

                                $entry->comments = '[Cutoff date!] Record stored in server but not reconciled!';
                            }
                            else{

                                $entry->comments = 'Record stored in server but not reconciled!';
                            }
                            $entry->save();
                        }
                    }

                    foreach ($no_reconcile_records_excel as $no_reconcile_record){
                        if( ReportExcelNoReconciliateRecord::where('id_de_transaccion', '=', $no_reconcile_record['id_de_transaccion'])->count() == 0  ){
                            $entry = new ReportExcelNoReconciliateRecord();
                            $entry->fecha_de_la_transaccion = $no_reconcile_record['fecha_de_la_transaccion']->toDateString();
                            $entry->id_de_transaccion = $no_reconcile_record['id_de_transaccion']; 
                            $entry->cliente = $no_reconcile_record['cliente'];
                            $entry->ubicacion_de_compra = $no_reconcile_record['ubicacion_de_compra'];
                            $entry->numero_de_transaccion = $no_reconcile_record['numero_de_transaccion'];
                            $entry->nombre_de_la_tarjeta = $no_reconcile_record['nombre_de_la_tarjeta'];
                            $entry->pieza = $no_reconcile_record['pieza'];
                            $entry->cantidad_litros = $no_reconcile_record['cantidad'];
                            $entry->total_del_solicitante = $no_reconcile_record['total_del_solicitante'];

                            $entry->save();
                        }
                    }

            })->get()->toArray();
        }
       
        //Return reconcile_records, no_reconcile_server_records and no reconcile excel records
        $VehicleReconciledRecords = VehicleReconciledRecord::all()->toArray();
        $VehicleNoReconciledRecords = VehicleNoReconciledRecord::all()->toArray();

        $reconcile_records =  array();
        $no_reconcile_server_records = array();
        $no_reconcile_excel_records = array();

        foreach ($VehicleReconciledRecords as $record){

           $temp_record = VehicleUsageRecord::where('id', '=', $record['vehicle_usage_record_id'])->get()->toArray();
            if($temp_record != null){  
                $temp_record = $this->records->getRecordInfo($record['vehicle_usage_record_id']);
                array_push($reconcile_records, $temp_record);
            }
        }

        foreach ($VehicleNoReconciledRecords as $record){

           $temp_record = VehicleUsageRecord::where('id', '=', $record['vehicle_usage_record_id'])->get()->toArray();

           if($temp_record != null){

                if($record['comments'] =='[Cutoff date!] Record stored in server but not reconciled!'){
                    $temp_record = $this->records->getRecordInfo($record['vehicle_usage_record_id']);
                    $temp_record['WARNING'] = '[Cutoff date!] Record stored in server but not reconciled!';
                    array_push($no_reconcile_server_records, $temp_record);
                }
                else if($record['comments'] =='Record stored in server but not reconciled!'){
                    $temp_record = $this->records->getRecordInfo($record['vehicle_usage_record_id']);
                    $temp_record['WARNING'] = 'Record stored in server but not reconciled!';
                    array_push($no_reconcile_server_records, $temp_record);
                }
                
           }
           

        }

        $excel_no_reconciliated_record = ExcelNoReconciliateRecord::all()->toArray();
        $excel_no_reconciliated_records = [];
        foreach ($excel_no_reconciliated_record as $record){
            $entry = [
                'número_de_cliente' => $record['cliente'],
                'fecha_de_la_transaccion' => $record['fecha_de_la_transaccion'],
                'ubicacion_de_compra' => $record['ubicacion_de_compra'],
                'numero_de_transaccion' => $record['numero_de_transaccion'],
                'nombre_de_la_tarjeta' => $record['nombre_de_la_tarjeta'],
                'tipo_de_gasolina' => $record['pieza'],
                'cantidad_litros' => $record['cantidad_litros'],
                'total_del_solicitante' => $record['total_del_solicitante'],
                'WARNING' => 'Record no reconcilied and is not stored in server'
            ];
            $excel_no_reconciliated_records[] = $entry;
        }

         //Conciliation Percent
         $conciliation_percent = number_format(((count($reconcile_records) / count($excel_records))*100), 2, '.', '');

         //Count de records en total
         $total_excel_records = count($excel_records);

         //Count of our server records
         $date_from = $excel_records[0]['fecha_de_la_transaccion'];
         $date_to = $excel_records[0]['fecha_de_la_transaccion'];
             
         //To find the date range in the excell record
         foreach ($excel_records as $record){

             if( $date_from->gt($record['fecha_de_la_transaccion'])){
                $date_from = $record['fecha_de_la_transaccion'];
             }
             if( $date_to->lt($record['fecha_de_la_transaccion'])){
                $date_to = $record['fecha_de_la_transaccion'];
             }
         }

        //Query to find the server records who are inside in the date range of the excel record.
        $server_records = VehicleUsageRecord::whereBetween('date', [$date_from->startOfDay(), $date_to->endOfDay()])->get()->toArray();
        $total_server_records = count($server_records);

        //Total de Gastos en total
        $total_expenses_in_excel_records = 0;
        foreach ($excel_records as $record){
            $total_expenses_in_excel_records += $record['total_del_solicitante'];
        }
        $total_expenses_in_excel_records = number_format($total_expenses_in_excel_records, 2, '.', '');
        
        //Total de gastos en nuestros server
        $total_expenses_in_server_records = 0;
        foreach ($server_records as $record){
            $total_expenses_in_server_records += $record['total_receipt'];
        }
        $total_expenses_in_server_records = number_format($total_expenses_in_server_records, 2, '.', '');

        //Generate Notifications
        foreach ($no_reconcile_server_records as $record){

            if( Notification::where('record_id', '=', $record['id'])->count() == 0  ){

                $notification = new Notification();
                $notification->custodian_id = $record['custodian_id'];
                $notification->record_id = $record['id'];
                $notification->was_read = false;
                $notification->was_justified = false;
                $notification->was_archived = false;
                $notification->status_type_id = 1; //status_type_name = 'Pending for custodian justification' 
                $notification->due_date = Carbon::today()->addWeeks(1);


                if($record['WARNING'] == 'Record stored in server but not reconciled!'){
                    $notification->notification_type_id = 1; //notification_type_name = 'Not reconcile by: Data Entry Error'
                }
                if($record['WARNING'] == '[Cutoff date!] Record stored in server but not reconciled!'){
                    $notification->notification_type_id = 2; //notification_type_name = 'Not reconcile by: Cutoff Date'
                }
                $notification->save();

            }
            
        }

        //Save conciliation stats details
        $today = Carbon::today();
        $this_month = $today->year.'-'.$today->month;
        $conciliation_dates = $date_from->toDateString().' / '.$date_to->toDateString();
        $formatted_conciliation_dates = $date_from->formatLocalized('%B %d, %Y').' to '.$date_to->formatLocalized('%B %d, %Y'); 
        if( ReportStatsDetails::where('conciliation_dates', '=', $conciliation_dates )->count() == 0  ){
            $stats_details = new ReportStatsDetails();
            $stats_details->conciliation_dates = $conciliation_dates;
            $stats_details->formatted_conciliation_dates = $formatted_conciliation_dates;
            $stats_details->conciliation_date_from = $date_from;
            $stats_details->conciliation_date_to = $date_to;
            $stats_details->conciliation_percent = $conciliation_percent;
            $stats_details->total_excel_records = $total_excel_records;
            $stats_details->total_server_records = $total_server_records;
            $stats_details->total_expenses_in_excel_records = $total_server_records;
            $stats_details->total_expenses_in_server_records = $total_expenses_in_excel_records;
            $stats_details->after_conciliation_percent = 0;
            $stats_details->save();
        }


        //Reconciliation Output
        $data=['conciliation_percent' => $conciliation_percent, 'total_excel_records' => $total_excel_records,'total_server_records' => $total_server_records, 'total_expenses_in_excel_records' => $total_expenses_in_excel_records,'total_expenses_in_server_records' => $total_expenses_in_server_records ,'reconciled_server_records' => $reconcile_records, 'no_reconciled_server_records' =>$no_reconcile_server_records, 'excel_no_reconciliated_records' => $excel_no_reconciliated_records];

        return response()->json(['data' => $data], 200);
    }


}