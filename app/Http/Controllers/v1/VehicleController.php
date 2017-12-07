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

    public function index(Request $request)
    {
        $user = $this->vehicles->getAuthenticatedUser();
        if($user->user_type_name == 'admin'){

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

    public function store(Request $request)
    {   
        $user = $this->vehicles->getAuthenticatedUser();
        if($user->user_type_name == 'admin'){

            $vehicle = $this->vehicles->createVehicle($request);

            return response()->json(['message' => 'Vehicle created successfully!',$vehicle], 201);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can create new vehicles.'], 401);
        }

    }

    public function show($id)
    {   
        $user = $this->vehicles->getAuthenticatedUser();
        $data = $this->vehicles->getVehicleInfo($id);

        if($data == null ){
            return response()->json(['message' => 'Error: Vehicle not found!'],404);
        }
        else if($user->user_type_name == 'admin'){
            return response()->json(['data' => $data], 200);
        }
        else{
            return response()->json(['message' => 'Error: Only Admin can view this info.'], 500);
        }
    }

    public function update(Request $request, $id)
    {   
        $user = $this->vehicles->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){
            return $this->vehicles->updateVehicle($request, $id);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can update vehicles.'], 401);
        }
    }

    public function destroy($id)
    {   
        $user = $this->vehicles->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){
            return $this->vehicles->deleteVehicle($id);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can delete vehicles.'], 401);
        }

    }


}