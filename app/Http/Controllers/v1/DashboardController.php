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

    //Need to finish stats()...
    public function stats(){   

        $user = $this->cards->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){

            $custodians_count = Custodian::all()->count();
            $cards_count = Card::where('status', '=', 'Active' )->count();
            $vehicles_count = Vehicle::all()->count();

            //$this_month = Carbon::now()->month;
            //$total_monthly_expenses = VehicleUsageRecord::where('date', '=', '2017-06')->get();
            //dd($total_monthly_expenses);

            $stats = [
                'registered_users' => $custodians_count,
                'active_credit_cards' => $cards_count,
                'registered_vehicles' => $vehicles_count
            ];


            return response()->json(['stats' => $stats], 200);

        }
        else {
            return response()->json(['message' => 'Error: Only Admin can view stats.'], 401);
        }

    }

    


}