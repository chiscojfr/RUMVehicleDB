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
use App\Services\v1\CustodiansService;
use App\Services\v1\CardsService;
use App\Services\v1\VehiclesService;

class VehicleController extends Controller
{
	public function __construct(VehiclesService $service){
        $this->vehicles = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | Get vehicle info depending user role.
    |--------------------------------------------------------------------------
    |
    | Param: Filter parameters
    | Return: Vehicle info
    |
    */
    public function index(Request $request)
    {
        $user = $this->vehicles->getAuthenticatedUser();
        if($user->user_type_name == 'admin' || $user->user_type_name == 'vehicle_admin'){

            $data = $this->vehicles->getVehicles($request);

            return response()->json(['data' => $data], 200);
        }
        else {

             $data = $this->vehicles->getUserVehicles($user->id);
             if(empty($data)){
             	return response()->json(['message' => 'Error: This custodian has not have vehicles.'], 404);
             }
             return response()->json(['data' => $data], 200);
        }
        
    }

    /*
    |--------------------------------------------------------------------------
    | Create new vehicle.
    |--------------------------------------------------------------------------
    |
    | Param: New vehicle request
    | Return: New veicle info
    |
    */
    public function store(Request $request)
    {   
        $user = $this->vehicles->getAuthenticatedUser();
        if($user->user_type_name == 'admin' || $user->user_type_name == 'vehicle_admin'){

            $vehicle = $this->vehicles->createVehicle($request);

            return response()->json(['message' => 'Vehicle created successfully!',$vehicle], 201);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can create new vehicles.'], 401);
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Get single vehicle info
    |--------------------------------------------------------------------------
    |
    | Param: vehicle_id
    | Return: Vehicle info
    |
    */
    public function show($id)
    {   
        $user = $this->vehicles->getAuthenticatedUser();
        $data = $this->vehicles->getVehicleInfo($id);

        if($data == null ){
            return response()->json(['message' => 'Error: Vehicle not found!'],404);
        }
        else if($user->user_type_name == 'admin' || $user->user_type_name == 'vehicle_admin'){
            return response()->json(['data' => $data], 200);
        }
        else{
            return response()->json(['message' => 'Error: Only Admin can view this info.'], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update vehicle info 
    |--------------------------------------------------------------------------
    |
    | Param: Request, vehicle_id 
    | Reutrn: Updated vehicle info
    |
    */
    public function update(Request $request, $id)
    {   
        $user = $this->vehicles->getAuthenticatedUser();

        if($user->user_type_name == 'admin' || $user->user_type_name == 'vehicle_admin'){
            return $this->vehicles->updateVehicle($request, $id);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can update vehicles.'], 401);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete vehicle  
    |--------------------------------------------------------------------------
    |
    | Param: vehicle_id 
    | Return: sucessful message
    |
    */
    public function destroy($id)
    {   
        $user = $this->vehicles->getAuthenticatedUser();

        if($user->user_type_name == 'admin' || $user->user_type_name == 'vehicle_admin'){
            return $this->vehicles->deleteVehicle($id);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can delete vehicles.'], 401);
        }

    }


}