<?php

namespace App\Services\v1;
use App\Custodian;
use App\UserType;
use App\Department;
use App\Card;
use App\Vehicle;
use App\VehicleUsageRecord;
use JWTAuth;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Request;


class VehicleUsageRecordService {

	public function createRecord(Request $request){

		$file = Request::file('filename');
		$extension = $file->getClientOriginalExtension();
		Storage::disk('local')->put($file->getFilename().'.'.$extension,  File::get($file));
		$entry = new VehicleUsageRecord();

		$entry->receipt_number = Request::input('receipt_number');
		$entry->date = Request::input('date');
		$entry->provider_number = Request::input('provider_number');
		$entry->purchase_type = Request::input('purchase_type');
		$entry->total_liters = Request::input('total_liters');
		$entry->total_receipt = Request::input('total_receipt');
		$entry->vehicle_mileage = Request::input('vehicle_mileage');
		$entry->vehicle_id = Request::input('vehicle_id');
		$entry->card_id = Request::input('card_id');
		$entry->custodian_id = Request::input('custodian_id');
		$entry->comments = Request::input('comments');

		$entry->mime = $file->getClientMimeType();
		$entry->original_filename = $file->getClientOriginalName();
		$entry->filename = $file->getFilename().'.'.$extension;

		$entry->save();

        return $entry;
	}

	//Pendiente
	public function updateRecord(Request $request, $id){

		$entry = VehicleUsageRecord::find($id);
		if ($entry == null){
			return response() -> json(['message' => 'Record not found!'], 404);
		}
		else{

	        $file = Request::file('filename');
	        dd($file);
			$extension = $file->getClientOriginalExtension();
			Storage::disk('local')->put($file->getFilename().'.'.$extension,  File::get($file));
			$entry = new VehicleUsageRecord();

			$entry->receipt_number = Request::input('receipt_number');
			$entry->date = Request::input('date');
			$entry->provider_number = Request::input('provider_number');
			$entry->purchase_type = Request::input('purchase_type');
			$entry->total_liters = Request::input('total_liters');
			$entry->total_receipt = Request::input('total_receipt');
			$entry->vehicle_mileage = Request::input('vehicle_mileage');
			$entry->vehicle_id = Request::input('vehicle_id');
			$entry->card_id = Request::input('card_id');
			$entry->custodian_id = Request::input('custodian_id');
			$entry->comments = Request::input('comments');

			$entry->mime = $file->getClientMimeType();
			$entry->original_filename = $file->getClientOriginalName();
			$entry->filename = $file->getFilename().'.'.$extension;

			$entry->update();

	        return response() -> json(['message' => 'The record has been updated!', 'data' =>$entry], 200);
    	}
	}

	public function getRecords(){
		return $this->filterRecords(VehicleUsageRecord::all());
	}

	public function getRecordInfo($id){

		$record = VehicleUsageRecord::find($id);
		if($record == null){
			$data = $record;
			if($data == null){
				return $data;
			}
        }
        else{

	        $data = [];

			$record_department_name = Department::find( Card::find($record->card_id)->department_id )->name;
	        $record->record_department_name =  $record_department_name;

	        $record_custodian_name = Custodian::find($record->custodian_id)->name;
	        $record->record_custodian_name =  $record_custodian_name;

	        $record_card_name = Card::find($record->card_id)->name;
	        $record->record_card_name =  $record_card_name;

	        $record_vehicle_vin = Vehicle::find($record->vehicle_id)->vin;
	        $record->record_vehicle_vin =  $record_vehicle_vin;

	        $picture = route('getentry', $record->filename);
	        $record->record_picture = $picture;

			$data = [
				'id' => $record->id,
				'receipt_number' => $record->receipt_number,
				'date' => $record->date,
				'provider_number' => $record->provider_number,
				'purchase_type' => $record->purchase_type,
				'total_liters' => $record->total_liters,
				'total_receipt' => $record->total_receipt,
				'vehicle_mileage' => $record->vehicle_mileage,
				'vehicle_id' => $record->vehicle_id,
				'card_id' => $record->card_id,
				'custodian_id' => $record->custodian_id,
				'comments' => $record->comments,
				'record_department_name' => $record->record_department_name,
				'record_custodian_name' => $record->record_custodian_name,
				'record_card_name' => $record->record_card_name,
				'record_vehicle_vin' => $record->record_vehicle_vin,
				'record_picture'=>$record->record_picture
			];

			return $data;
		}
	}

	public function getUserRecords($id){

		$record = VehicleUsageRecord::where('custodian_id', $id)->get();
		return $this->filterRecords($record);
		
	}

	public function deleteRecord($id){

		$vehicle = VehicleUsageRecord::find($id);
		if ($vehicle == null){
			return response() -> json(['message' => 'Vehicle not found!'], 404);
		}
		else{

			$vehicle = VehicleUsageRecord::destroy($id);

            return response() -> json(['message' => 'The record has been successfully deleted!'], 200);
		}
		
	}

	protected function filterRecords($records) {
		$data = [];
		foreach ($records as $record){

			$record_department_name = Department::find( Card::find($record->card_id)->department_id )->name;
	        $record->record_department_name =  $record_department_name;

	        $record_custodian_name = Custodian::find( $record->custodian_id )->name;
	        $record->record_custodian_name =  $record_custodian_name;

	        $record_card_name = Card::find($record->card_id)->name;
	        $record->record_card_name =  $record_card_name;

	        $record_vehicle_vin = Vehicle::find($record->vehicle_id)->vin;
	        $record->record_vehicle_vin =  $record_vehicle_vin;

	        $picture = route('getentry', $record->filename);
	        $record->record_picture = $picture;

			$entry = [
				'id' => $record->id,
				'receipt_number' => $record->receipt_number,
				'date' => $record->date,
				'provider_number' => $record->provider_number,
				'purchase_type' => $record->purchase_type,
				'total_liters' => $record->total_liters,
				'total_receipt' => $record->total_receipt,
				'vehicle_mileage' => $record->vehicle_mileage,
				'vehicle_id' => $record->vehicle_id,
				'card_id' => $record->card_id,
				'custodian_id' => $record->custodian_id,
				'comments' => $record->comments,
				'record_department_name' => $record->record_department_name,
				'record_custodian_name' => $record->record_custodian_name,
				'record_card_name' => $record->record_card_name,
				'record_vehicle_vin' => $record->record_vehicle_vin,
				'record_picture'=>$record->record_picture
			];
			$data[] = $entry;
		}

		return $data;
	}

	public function getAuthenticatedUser(){

        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        $user_type_name = Usertype::find($user->user_type_id)->role;
        $user->user_type_name =  $user_type_name;

        $user_department_name = Department::find($user->department_id)->name;
        $user->user_department_name =  $user_department_name;

        // the token is valid and we have found the user via the sub claim
        return $user;
    }

}
