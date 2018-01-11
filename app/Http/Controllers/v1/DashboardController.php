<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Custodian;
use App\UserType;
use App\Card;
use App\Vehicle;
use App\VehicleUsageRecord;
use App\Notification;
use App\NotificationType; 
use App\RecordCorrectionStatus;
use App\Services\v1\CardsService;
use Carbon\Carbon;
use App\ReportVehicleReconciledRecord;
use App\ReportVehicleNoReconciledRecord;
use App\ReportExcelNoReconciliateRecord;
use App\ReportStatsDetails;
use Excel;
use Faker\Factory as Faker;

class DashboardController extends Controller
{
	public function __construct(CardsService $service){
        $this->cards = $service;
    }
    
    /*
    |--------------------------------------------------------------------------
    | Dashboard stats 
    |--------------------------------------------------------------------------
    |
    | Param: n/a
    | Reutrn: (ADMIN) System stats about, total users, active cards, 
    |         registered vehicles and total monthly expenses.
    |         (Custodian) System stats about, custodian cards and monthly expenses.
    |
    */
    public function getStats(){  

        $user = $this->cards->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){
            return $this->adminStats();
        }
        elseif($user->user_type_name == 'custodian' || $user->user_type_name == 'auxiliary_custodian'){
            return $this->custodianStats();   
        }
        else{
            return response()->json(['message' => 'Error: Only Admin or Custodians can view stats.'], 401);
        }

     }

    /*
    |--------------------------------------------------------------------------
    | Admin dashboard stats 
    |--------------------------------------------------------------------------
    |
    | Param: n/a
    | Reutrn: System stats about, total users, active cards, 
    |         registered vehicles, total monthly expenses, and latest_conciliation_date.
    |
    */
    private function adminStats(){   

        $user = $this->cards->getAuthenticatedUser();

        $custodians_count = Custodian::all()->count();
        $cards_count = Card::where('status', '=', 'Active' )->count();
        $vehicles_count = Vehicle::all()->count();

        $today = Carbon::today();
        $first_day_of_this_month = new Carbon('first day of this month');
        $monthly_expenses = VehicleUsageRecord::whereBetween('date', [$first_day_of_this_month->startOfDay(), $today->endOfDay()])->get()->toArray();
        $total_monthly_expenses = 0;

        foreach ($monthly_expenses as $expense) {
            $total_monthly_expenses +=  $expense['total_receipt'];
        }

        $latest_conciliation_date = null;
        if(ReportStatsDetails::all()->count()){
            $latest_conciliation_date = ReportStatsDetails::latest('created_at')->get();
            $latest_conciliation_date = $latest_conciliation_date[0]['created_at']->toDateString();
        }

        $stats = [
            'registered_users' => $custodians_count,
            'active_credit_cards' => $cards_count,
            'registered_vehicles' => $vehicles_count,
            'total_monthly_expenses' => $total_monthly_expenses,
            'latest_conciliation_date' => $latest_conciliation_date
        ];

        return response()->json(['stats' => $stats], 200);

    }

    /*
    |--------------------------------------------------------------------------
    | Custodian dashboard stats 
    |--------------------------------------------------------------------------
    |
    | Param: n/a
    | Reutrn: System stats about, custodian cards and monthly expenses.
    |
    */
    private function custodianStats(){   

        $user = $this->cards->getAuthenticatedUser();

        $cards = Card::where('custodian_id', '=', $user->id )->get();
        $today = Carbon::today();
        $first_day_of_this_month = new Carbon('first day of this month');
        $monthly_expenses = VehicleUsageRecord::where('custodian_id','=', $user->id)->whereBetween('date', [$first_day_of_this_month->startOfDay(), $today->endOfDay()])->get()->toArray();
        $total_monthly_expenses = 0;
        foreach ($monthly_expenses as $expense) {
            $total_monthly_expenses +=  $expense['total_receipt'];
        }

        $stats = [
            'cards' => $cards,
            'total_monthly_expenses' => $total_monthly_expenses
        ];

        return response()->json(['stats' => $stats], 200);

    }

    /*
    |--------------------------------------------------------------------------
    | Custodian Notifications
    |--------------------------------------------------------------------------
    |
    | Param: n/a
    | Reutrn: Logged Admin and Custodian Notifications
    |
    */
    public function getNotifications(){  

        $user = $this->cards->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){
            return $this->getAdminNotifications();
        }

        elseif($user->user_type_name == 'custodian'){
            return $this->getCustodianNotifications();   
        }
        else{
            return response()->json(['message' => 'Error: Only Admin or Custodians can view stats.'], 401);
        }

     }

    /*
    |--------------------------------------------------------------------------
    | Custodian Notifications
    |--------------------------------------------------------------------------
    |
    | Param: n/a
    | Reutrn: Logged Custodian Notifications
    |
    */
    private function getCustodianNotifications(){   

        $user = $this->cards->getAuthenticatedUser();

        if( Notification::where('custodian_id', '=', $user->id)->count() > 0 ){

            $notifications = Notification::where('custodian_id', '=', $user->id);
            ///$notifications = Notification::where('was_archived', '=', 0);
            $unread_notifications_count = $notifications->where('was_read','=','0')->count();
            $notifications = $notifications->get()->toArray();
            $data = [];
            foreach ($notifications as $notification) {
                $record = $this->cards->getRecordInfo($notification['record_id']);
                $notification_type_name = NotificationType::find($notification['notification_type_id'])->notification_type_name;
                $record_correction_status = RecordCorrectionStatus::find($notification['status_type_id'])->status_type_name;

                $entry =[
                    'id' => $notification['id'],
                    'notification_type' => $notification_type_name,
                    'record_correction_status' => $record_correction_status,
                    'was_read' => $notification['was_read'],
                    'was_justified' => $notification['was_justified'],
                    'due_date' => $notification['due_date'],
                    'was_archived' => $notification['was_archived'],
                    'record_id' => $notification['record_id'],
                    'record_info' => $record,
                ];
                $data[] = $entry;
            }
          
            return response()->json(['notifications' => $data, 'unread_notifications_count' => $unread_notifications_count], 200);
        }
        else{
            return response()->json(['notifications' => 0], 200);
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Admin Notifications
    |--------------------------------------------------------------------------
    |
    | Param: n/a
    | Reutrn: Logged Admin Notifications
    |
    */
    private function getAdminNotifications(){   

        $user = $this->cards->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){
            
            $notifications = Notification::where('was_justified', '=', 1)->get()->toArray();
            $justified_notifications_count = Notification::where('was_justified', '=', 1)->count();

            $data = [];
            foreach ($notifications as $notification) {
                $record = VehicleUsageRecord::where('id', '=', $notification['record_id'])->get()->toArray();
                $notification_type_name = NotificationType::find($notification['notification_type_id'])->notification_type_name;
                $record_correction_status = RecordCorrectionStatus::find($notification['status_type_id'])->status_type_name;

                $entry =[
                    'id' => $notification['id'],
                    'notification_type' => $notification_type_name,
                    'record_correction_status' => $record_correction_status,
                    'was_justified' => $notification['was_justified'],
                    'justification' => $notification['justification'],
                    'record_id' => $notification['record_id'],
                    'record_info' => $record,
                ];
                $data[] = $entry;
            }
          
            return response()->json(['notifications' => $data, 'justified_notifications_count' => $justified_notifications_count], 200);
        }
        else{
            return response()->json(['message' => 'Error: Only Admin can view their notifications.'], 401);
        }

    }

     /*
    |--------------------------------------------------------------------------
    | Edit Notifications: 'was read', 'was justified', 'justification text'
    |--------------------------------------------------------------------------
    |
    | Param: $request, $id
    | Reutrn: Updated notification
    |
    */
    public function notificationUpdate(Request $request, $id){

        $user = $this->cards->getAuthenticatedUser();

        $notification = Notification::find($id);
        if($notification == null){
            return response() -> json(['message' => 'Notification not found!'], 404);
        }
        else{

            if($user->user_type_name == 'admin'){
                $notification->fill($request->all());
                $notification->save();
                return response() -> json(['message' => 'The notification has been updated!', 'data' =>$notification], 200);
            }
            elseif ($user->user_type_name == 'custodian' || $user->user_type_name == 'auxiliary_custodian') {
                
                if(request()->has('was_read')){
                    $notification->was_read = $request['was_read'];
                }   

                if(request()->has('justification')){
                    $notification->justification = $request['justification'];
                    $notification->was_justified = 1;
                    $notification->status_type_id = 2;
                }

                $notification->save();
                return response() -> json(['message' => 'The notification has been updated!', 'data' =>$notification], 200);
            }
            
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Monthly Report
    |--------------------------------------------------------------------------
    |
    | Param: $request (month)
    | Reutrn: Generated .xls report
    |
    */
    public function generateMonthlyReport(Request $request){

            $record_stats_details = ReportStatsDetails::where('conciliation_dates', '=', $request['dates'] )->get();
            $date_from = new Carbon($record_stats_details[0]['conciliation_date_from']); 
            $date_from = $date_from->startOfDay()->subMinute();
            $date_to = $record_stats_details[0]['conciliation_date_to'];

            $VehicleReconciledRecords = ReportVehicleReconciledRecord::whereBetween('record_date', [$date_from, $date_to])->get(); 
            $excel_no_reconciliated_records = ReportExcelNoReconciliateRecord::whereBetween('fecha_de_la_transaccion', [$date_from, $date_to])->get(); 
            $justified_no_reconcile_server_records = Notification::whereBetween('record_date', [$date_from, $date_to])->get();
            
            $reconcile_records =  array();
            $no_reconcile_server_records = array();

            foreach ($VehicleReconciledRecords as $record){
               $temp_record = VehicleUsageRecord::where('id', '=', $record['vehicle_usage_record_id'])->get()->toArray();
                if($temp_record != null){  
                    $temp_record = $this->cards->getRecordInfo($record['vehicle_usage_record_id']);
                    array_push($reconcile_records, $temp_record);
                }
            }

            foreach ($justified_no_reconcile_server_records as $record){

               $temp_record = VehicleUsageRecord::where('id', '=', $record['record_id'])->get()->toArray();

               if($temp_record != null){

                    $temp_record = $this->cards->getRecordInfo($record['record_id']);
                    $temp_record['system_warning'] = NotificationType::find($record['notification_type_id'])->notification_type_name;
                    $temp_record['custodian_justification'] = $record['justification'];
                    $temp_record['justification_status'] = RecordCorrectionStatus::find($record['status_type_id'])->status_type_name;
                    array_push($no_reconcile_server_records, $temp_record);    
               }

            }

            $data = [ 'record_stats_details' => $record_stats_details,'reconcile_records' => $reconcile_records, 'no_reconcile_server_records' => $no_reconcile_server_records, 'excel_no_reconciliated_records' => $excel_no_reconciliated_records ];

            Excel::create('Conciliation Report from '.$record_stats_details[0]['formatted_conciliation_dates'], function($excel) use($data) {

                $excel->sheet('Sheet', function($sheet) use($data) {

                    $sheet->setFontFamily('Arial Rounded MT Bold');
                    $sheet->setFontSize(20);
                    $sheet->setFontBold(true);
                    $sheet->sethorizontalCentered(true);

                    $sheet->mergeCells('A1:N1');
                    $sheet->mergeCells('A2:N2');
                    $sheet->setFontSize(18);
                    $sheet->setAllBorders('thin');
                    $sheet->mergeCells('A3:N3');
                    $sheet->mergeCells('A4:N4');
                    $sheet->mergeCells('A5:N5');
                    $sheet->mergeCells('A6:N6');
                    $sheet->mergeCells('A7:N7');
                    $sheet->mergeCells('A8:N8');
                    $sheet->mergeCells('A9:N9');
                    $sheet->row(1, ['UNIVERSIDAD DE PUERTO RICO RECINTO DE MAYAGÜEZ']);
                    $sheet->row(2, ['DECANATO DE ADMINISTRACIÓN']);
                    $sheet->row(1, function($row) { $row->setBackground('#bfff7f'); });
                    $sheet->row(2, function($row) { $row->setBackground('#bfff7f'); });
                    $sheet->row(3, function($row) { $row->setBackground('#fceb97'); });
                    $sheet->row(4, function($row) { $row->setBackground('#fceb97'); });
                    $sheet->row(5, function($row) { $row->setBackground('#fceb97'); });
                    $sheet->row(6, function($row) { $row->setBackground('#fceb97'); });
                    $sheet->row(7, function($row) { $row->setBackground('#fceb97'); });
                    $sheet->row(8, function($row) { $row->setBackground('#fceb97'); });
                    $sheet->row(9, function($row) { $row->setBackground('#fceb97'); });
                    
                    $sheet->row(3, ['']);
                    $sheet->row(4, ['Conciliation Report from '.$data['record_stats_details'][0]['formatted_conciliation_dates']]);
                    $sheet->row(5, ['Conciliation Percent: '.$data['record_stats_details'][0]['conciliation_percent'].'%' ]);
                    $sheet->row(6, ['']);
                    $sheet->row(7, ['System Transactions: '.$data['record_stats_details'][0]['total_server_records'].' | '.' Total expenses: $'.$data['record_stats_details'][0]['total_expenses_in_server_records'] ]);
                    $sheet->row(8, ['                                            VS.']);
                    $sheet->row(9, ['Total Petroleum Transactions: '.$data['record_stats_details'][0]['total_excel_records'].' | '. ' Total expenses: $'.$data['record_stats_details'][0]['total_expenses_in_excel_records'] ]);

                    $sheet->setFontSize(15);
                    $sheet->appendRow(['']);
                    $sheet->appendRow(['RECONCILE RECORDS']);
                    $currentRow = sizeof($sheet->appendRow(['Date','Receipt Number', 'Purchase Type', 'Total Liters', 'Total Receipt', 'Vehicle Mileage', 'Comments', 'Department', 'Custodian', 'Card Name', 'Card Number'])->toArray());
                    $sheet->row($currentRow, function($row) { $row->setBackground('#bfff7f'); });
                    $sheet->setFontSize(12);
                    $sheet->setColumnFormat(array(
                        'K' => '0'
                    ));

                    foreach ($data['reconcile_records'] as $record) {
                        $row = [];
                        $row[0] = $record['date'];
                        $row[1] = $record['receipt_number'];
                        $row[2] = $record['purchase_type'];
                        $row[3] = $record['total_liters'];
                        $row[4] = $record['total_receipt'];
                        $row[5] = $record['vehicle_mileage'];
                        $row[6] = $record['comments'];
                        $row[7] = $record['record_department_name'];
                        $row[8] = $record['record_custodian_name'];
                        $row[9] = $record['record_card_name'];
                        $row[10] = $record['record_card_number'];
                        $sheet->appendRow($row);
                    }

                    $sheet->appendRow(['']);
                    $sheet->appendRow(['']);
                    $sheet->setFontSize(15);
                    $sheet->appendRow(['NON RECONCILE RECORDS']);
                    $currentRow = sizeof($sheet->appendRow(['Date','Receipt Number', 'Purchase Type', 'Total Liters', 'Total Receipt', 'Vehicle Mileage', 'Comments', 'Department', 'Custodian', 'Card Name', 'Card Number', 'System Warning', 'Custodian Justification', 'Justification Status'])->toArray());
                    $sheet->row($currentRow, function($row) { $row->setBackground('#bfff7f'); });
                    $sheet->setFontSize(12);
                    foreach ($data['no_reconcile_server_records'] as $record) {
                        $row = [];
                        $row[0] = $record['date'];
                        $row[1] = $record['receipt_number'];
                        $row[2] = $record['purchase_type'];
                        $row[3] = $record['total_liters'];
                        $row[4] = $record['total_receipt'];
                        $row[5] = $record['vehicle_mileage'];
                        $row[6] = $record['comments'];
                        $row[7] = $record['record_department_name'];
                        $row[8] = $record['record_custodian_name'];
                        $row[9] = $record['record_card_name'];
                        $row[10] = $record['record_card_number'];
                        $row[11] = $record['system_warning'];
                        $row[12] = $record['custodian_justification'];
                        $row[13] = $record['justification_status'];
                        $sheet->appendRow($row);
                        
                    }

                    $sheet->appendRow(['']);
                    $sheet->appendRow(['']);
                    $sheet->setFontSize(15);
                    $sheet->appendRow(['TOTAL PETROLEUM NON RECONCILE RECORDS']);
                    $currentRow = sizeof($sheet->appendRow(['Date','Purchase Location','Receipt Number', 'Purchase Type', 'Total Liters', 'Total Receipt', 'Card Name','Customer Name'])->toArray());
                    $sheet->row($currentRow, function($row) { $row->setBackground('#bfff7f'); });
                    $sheet->setFontSize(12);
                    foreach ($data['excel_no_reconciliated_records'] as $record) {
                        $row = [];
                        $row[0] = $record['fecha_de_la_transaccion'];
                        $row[1] = $record['ubicacion_de_compra'];
                        $row[2] = $record['numero_de_transaccion'];
                        $row[3] = $record['pieza'];
                        $row[4] = $record['cantidad_litros'];
                        $row[5] = $record['total_del_solicitante'];
                        $row[6] = $record['nombre_de_la_tarjeta'];
                        $row[7] = $record['cliente'];
                        $sheet->appendRow($row);
                    }

                });

            })->download('xls');

    }

    /*
    |--------------------------------------------------------------------------
    | Report Dates
    |--------------------------------------------------------------------------
    |
    | Param: n/a
    | Reutrn: Conciliation Dates
    |
    */
    public function reportDates(){

        // $faker = Faker::create();
        // dd($faker->password);
     // $values = [];   
     // $values = array(
     //      array('a'=>0, 'x'=>1, 'y'=>2, 'z'=>3),
     //      array('foo'=>"bar", 'x'=>4, 'y'=>5),
     //      array('x'=>-1, 'y'=>-2, 'z'=>"baz"),
     //      array('x'=>-1, 'y'=>-2, 'z'=> array('m'=>123)),
     //    );
     // foreach ($values as $value ) {
     //    dd( $this->has( $value['a'] ) );
     //    if($t['a'] =! null ){

     //    }
     // }
        

        $details = ReportStatsDetails::all();

        $data = [];
        foreach ($details as $detail) {

            $entry =[
                    'value' => $detail['conciliation_dates'],
                    'formatted_conciliation_dates' => $detail['formatted_conciliation_dates'],
                    ];

            $data[] = $entry;
        }
        return response()->json(['data' => $data], 200);


    }


}