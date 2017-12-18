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
use App\Services\v1\CardsService;
use Carbon\Carbon;

class DashboardController extends Controller
{
	public function __construct(CardsService $service){
        $this->cards = $service;
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
    public function stats(){   

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
    | Custodian Notifications
    |--------------------------------------------------------------------------
    |
    | Param: n/a
    | Reutrn: Logged Custodian Notifications
    |
    */
    public function getCustodianNotifications(){   

        $user = $this->cards->getAuthenticatedUser();

        if( Notification::where('custodian_id', '=', $user->id)->count() > 0 ){
            $notifications = Notification::where('custodian_id', '=', $user->id);
            $notifications = Notification::where('was_read', '=', 0)->get()->toArray();
            //dd($notifications);

            $data = [];
            foreach ($notifications as $notification) {
                $record = VehicleUsageRecord::where('id', '=', $notification['record_id'])->get()->toArray();
                $notification_type_name = NotificationType::find($notification['notification_type_id'])->notification_type_name;
                $entry =[
                    'id' => $notification['id'],
                    'notification_type' => $notification_type_name,
                    'was_read' => $notification['was_read'],
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
    | Mark Notifications as 'was read'
    |--------------------------------------------------------------------------
    |
    | Param: $request, $id
    | Reutrn: Updated notification
    |
    */
    public function notificationWasRead(Request $request, $id){
        $notification = Notification::find($id);
        if($notification == null){
            return response() -> json(['message' => 'Notification not found!'], 404);
        }
        else{
            $notification->fill($request->all());
            $notification->save();
            return response() -> json(['message' => 'The notification was read!', 'data' =>$notification], 200);
        }
    }


}