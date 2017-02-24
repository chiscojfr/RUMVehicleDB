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
use App\Services\v1\CustodiansService;
use App\Services\v1\CardsService;
use App\Services\v1\VehiclesService;
use App\Services\v1\VehicleUsageRecordService;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;

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

    public function get($filename){
        $entry = VehicleUsageRecord::where('filename', '=', $filename)->firstOrFail();
        $file = Storage::disk('local')->get($entry->filename);
 
        return (new Response($file, 200))
              ->header('Content-Type', $entry->mime);
    }


}