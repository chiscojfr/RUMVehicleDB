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
    |         registered vehicles and total monthly expenses.
    |
    */
    private function adminStats(){   

        $user = $this->cards->getAuthenticatedUser();

        $custodians_count = Custodian::all()->count();
        $cards_count = Card::where('status', '=', 'Active' )->count();
        $vehicles_count = Vehicle::all()->count();

        $today = Carbon::today();
        $first_day_of_this_month = new Carbon('first day of this month');
        $monthly_expenses = VehicleUsageRecord::whereBetween('date', [$first_day_of_this_month->subDay(), $today->addDay()])->get()->toArray();
        $total_monthly_expenses = 0;
        foreach ($monthly_expenses as $expense) {
            $total_monthly_expenses +=  $expense['total_receipt'];
        }

        $stats = [
            'registered_users' => $custodians_count,
            'active_credit_cards' => $cards_count,
            'registered_vehicles' => $vehicles_count,
            'total_monthly_expenses' => $total_monthly_expenses
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
        $monthly_expenses = VehicleUsageRecord::where('custodian_id','=', $user->id)->whereBetween('date', [$first_day_of_this_month->subDay(), $today->addDay()])->get()->toArray();
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
            $notifications = Notification::where('was_archived', '=', 0)->get()->toArray();

            $data = [];
            foreach ($notifications as $notification) {
                $record = VehicleUsageRecord::where('id', '=', $notification['record_id'])->get()->toArray();
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
          
            return response()->json(['notifications' => $data], 200);
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
          
            return response()->json(['notifications' => $data], 200);
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


}