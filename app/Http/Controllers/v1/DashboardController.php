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
use App\Services\v1\CardsService;
use Carbon\Carbon;

class DashboardController extends Controller
{
	public function __construct(CardsService $service){
        $this->cards = $service;
    }

    public function index(){
        
    }

    //Return system stats about, total users, active cards, registered vehicles and total monthly expenses
    public function stats(){   

        $user = $this->cards->getAuthenticatedUser();

            $custodians_count = Custodian::all()->count();
            $cards_count = Card::where('status', '=', 'Active' )->count();
            $vehicles_count = Vehicle::all()->count();

            $today = Carbon::today();
            $first_day_of_this_month = new Carbon('first day of this month');
            $monthly_expenses = VehicleUsageRecord::whereBetween('date', [$first_day_of_this_month->subDay(), $today->addDay()])->get()->toArray();
            $total_monthly_expenses = 0;
            foreach ($monthly_expenses as $total_receipt) {
                $total_monthly_expenses +=  $total_receipt['total_receipt'];
            }

            $stats = [
                'registered_users' => $custodians_count,
                'active_credit_cards' => $cards_count,
                'registered_vehicles' => $vehicles_count,
                'total_monthly_expenses' => $total_monthly_expenses
            ];


            return response()->json(['stats' => $stats], 200);


    }

    


}