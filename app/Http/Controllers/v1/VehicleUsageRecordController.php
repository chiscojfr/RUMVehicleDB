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

class VehicleUsageRecordController extends Controller
{
    public function __construct(VehicleUsageRecordService $service){
        $this->records = $service;
    }

    public function index()
    {
        $user = $this->records->getAuthenticatedUser();
        if($user->user_type_name == 'admin'){

            $data = $this->records->getRecords();

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

    public function store(Request $request)
    {   
        $user = $this->records->getAuthenticatedUser();
        if($user->user_type_name == 'admin' || $user->user_type_name == 'custodian'){

            $record = $this->records->createRecord($request);

            return response()->json(['message' => 'Record created successfully!',$record], 201);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin or Custodians can create new records.'], 401);
        }

    }

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

    public function get($filename)
    {
        $entry = VehicleUsageRecord::where('filename', '=', $filename)->firstOrFail();
        $file = Storage::disk('local')->get($entry->filename);
 
        return (new Response($file, 200))
              ->header('Content-Type', $entry->mime);
    }

    public function filter(Request $request){
        
        $user = $this->records->getAuthenticatedUser();

            $filtered_data = $this->records->filter($request);
            return response()->json(['filtered_data' => $filtered_data], 200);  

    }

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
              
              Excel::selectSheetsByIndex(0)->load($route, function($sheet) {

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
                 $server_records = VehicleUsageRecord::whereBetween('date', [$date_from, $date_to])->get()->toArray();

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

                        if($no_reconcile_record['date'] == $date_from->toDateString()){

                            $entry->comments = '[Cutoff date!] Record stored in server but not reconciled!';
                        }
                        else{

                            $entry->comments = 'Record stored in server but not reconciled!';
                        }
                        $entry->save();
                    }

                    foreach ($no_reconcile_records_excel as $no_reconcile_record){
                        $entry = new ExcelNoReconciliateRecord();
                        $entry->fecha_de_la_transaccion = $no_reconcile_record['fecha_de_la_transaccion']->toDateString();
                        $entry->ubicacion_de_compra = $no_reconcile_record['ubicacion_de_compra'];
                        $entry->numero_de_transaccion = $no_reconcile_record['numero_de_transaccion'];
                        $entry->nombre_de_la_tarjeta = $no_reconcile_record['nombre_de_la_tarjeta'];
                        $entry->pieza = $no_reconcile_record['pieza'];
                        $entry->cantidad_litros = $no_reconcile_record['cantidad'];
                        $entry->total_del_solicitante = $no_reconcile_record['total_del_solicitante'];

                        $entry->save();
                    }
            });
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

        //Agregar los %

        $data=['reconciled_server_records' => $reconcile_records, 'no_reconciled_server_records' =>$no_reconcile_server_records, 'excel_no_reconciliated_records' => $excel_no_reconciliated_records];

        return response()->json(['data' => $data], 200);
    }


}