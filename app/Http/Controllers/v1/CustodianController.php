<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Custodian;
use App\UserType;
use App\Services\v1\CustodiansService;

class CustodianController extends Controller
{

    public function __construct(CustodiansService $service){
        $this->custodians = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | Get custodians info depending user role.
    |--------------------------------------------------------------------------
    |
    | Param: Filter parameters
    | Return: Custodians info
    |
    */
    public function index(Request $request)
    {
        $user = $this->custodians->getAuthenticatedUser();
        if($user->user_type_name == 'admin'){

            $data = $this->custodians->getCustodians($request);

            return response()->json(['data' => $data], 200);
        }
        else {
             $data = $this->custodians->getCustodianInfo($user->id);
             return response()->json(['data' => $data], 200);
        }
        
    }

    /*
    |--------------------------------------------------------------------------
    | Create new custodian.
    |--------------------------------------------------------------------------
    |
    | Param: New custodian request
    | Return: New custodian info
    |
    */
    public function store(Request $request)
    {   
        $user = $this->custodians->getAuthenticatedUser();
        if($user->user_type_name == 'admin'){

            $custodian = $this->custodians->createCustodian($request);

            return response()->json(['message' => 'Custodian created successfully!',$custodian], 201);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can create new custodians.'], 401);
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Get single custodian info
    |--------------------------------------------------------------------------
    |
    | Param: custodian_id
    | Return: Custodian info
    |
    */
    public function show($id)
    {   
        $user = $this->custodians->getAuthenticatedUser();
        $data = $this->custodians->getCustodianInfo($id);

        if($data == null ){
            return response()->json(['message' => 'Error: Custodian not found!'],404);
        }
        else if($user->user_type_name == 'admin'){
            return response()->json(['data' => $data], 200);
        }
        else{
            return response()->json(['message' => 'Error: Only Admin can view this info.'], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Update custodian info 
    |--------------------------------------------------------------------------
    |
    | Param: request, custodian_id 
    | Reutrn: Updated custodian  info
    |
    */
    public function update(Request $request, $id)
    {   
        $user = $this->custodians->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){
            return $this->custodians->updateCustodian($request, $id);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can update custodians.'], 401);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete custodian  
    |--------------------------------------------------------------------------
    |
    | Param: custodian_id 
    | Return: sucessful message
    |
    */
    public function destroy($id)
    {   
        $user = $this->custodians->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){
            return $this->custodians->deleteCustodian($id);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can delete custodians.'], 401);
        }

    }
}
