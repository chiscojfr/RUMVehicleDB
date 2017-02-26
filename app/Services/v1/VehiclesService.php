<?php

namespace App\Services\v1;
use App\Custodian;
use App\UserType;
use App\Department;
use App\Card;
use App\Vehicle;
use JWTAuth;

class VehiclesService {

	public function createVehicle($request){

		return Vehicle::create($request->all());

        return response() -> json(['message' => 'The vehicle has been created!'], 200);
	}

	public function updateVehicle($request, $id){

		$vehicle = Vehicle::find($id);
		if ($vehicle == null){
			return response() -> json(['message' => 'Vehicle not found!'], 404);
		}
		else{

	        $vehicle->fill($request->all());
	        $vehicle->save();

	        return response() -> json(['message' => 'The vehicle has been updated!', 'data' =>$vehicle], 200);
    	}
	}

	public function getVehicles(){
		return $this->filterVehicles(Vehicle::all());
	}

	public function getVehicleInfo($id){

		$vehicle = Vehicle::find($id);
		if($vehicle == null){
			$data = $vehicle;
			if($data == null){
				return $data;
			}
        }
        else{

			$vehicle_department_name = Department::find($vehicle->department_id)->name;
	        $vehicle->vehicle_department_name =  $vehicle_department_name;

	        $vehicle_custodian_name = Custodian::find($vehicle->custodian_id)->name;
	        $vehicle->vehicle_custodian_name =  $vehicle_custodian_name;

	        $data = [];

			$data = [
				'id' => $vehicle->id,
				'make' => $vehicle->make,
				'vin' => $vehicle->vin,
				'model' => $vehicle->model,
				'color' => $vehicle->color,
				'year' => $vehicle->year,
				'type' => $vehicle->type,
				'serial_number' => $vehicle->serial_number,
				'property_number' => $vehicle->property_number,
				'marbete_date' => $vehicle->marbete_date,
				'inspection_date' => $vehicle->inspection_date,
				'decomission_date' => $vehicle->decomission_date,
				'registration_id' => $vehicle->registration_id,
				'title_id' => $vehicle->title_id,
				'doors' => $vehicle->doors,
				'cylinders' => $vehicle->cylinders,
				'ACAA' => $vehicle->ACAA,
				'insurance' => $vehicle->insurance,
				'purchase_price' => $vehicle->purchase_price,
				'license_plate' => $vehicle->license_plate,
				'inscription_date' => $vehicle->inscription_date,
				'custodian_id' => $vehicle->custodian_id,
	            'department_id' => $vehicle->department_id,
	            'vehicle_custodian_name' => $vehicle->vehicle_custodian_name,
	            'vehicle_department_name' => $vehicle->vehicle_department_name
			];

			return $data;
		}
	}

	public function getUserVehicles($id){

		$vehicle = Vehicle::where('custodian_id', $id)->get();
		return $this->filterVehicles($vehicle);
		
	}

	public function deleteVehicle($id){

		$vehicle = Vehicle::find($id);
		if ($vehicle == null){
			return response() -> json(['message' => 'Vehicle not found!'], 404);
		}
		else{

			$vehicle = Vehicle::destroy($id);

            return response() -> json(['message' => 'The vehicle has been successfully deleted!'], 200);
		}
		
	}

	protected function filterVehicles($vehicles) {
		$data = [];
		foreach ($vehicles as $vehicle){

			$vehicle_department_name = Department::find($vehicle->department_id)->name;
	        $vehicle->vehicle_department_name =  $vehicle_department_name;

	        $vehicle_custodian_name = Custodian::find($vehicle->custodian_id)->name;
	        $vehicle->vehicle_custodian_name =  $vehicle_custodian_name;

			$entry = [
				'id' => $vehicle->id,
				'make' => $vehicle->make,
				'vin' => $vehicle->vin,
				'model' => $vehicle->model,
				'color' => $vehicle->color,
				'year' => $vehicle->year,
				'type' => $vehicle->type,
				'serial_number' => $vehicle->serial_number,
				'property_number' => $vehicle->property_number,
				'marbete_date' => $vehicle->marbete_date,
				'inspection_date' => $vehicle->inspection_date,
				'decomission_date' => $vehicle->decomission_date,
				'registration_id' => $vehicle->registration_id,
				'title_id' => $vehicle->title_id,
				'doors' => $vehicle->doors,
				'cylinders' => $vehicle->cylinders,
				'ACAA' => $vehicle->ACAA,
				'insurance' => $vehicle->insurance,
				'purchase_price' => $vehicle->purchase_price,
				'inscription_date' => $vehicle->inscription_date,
				'license_plate' => $vehicle->license_plate,
				'custodian_id' => $vehicle->custodian_id,
	            'department_id' => $vehicle->department_id,
	            'vehicle_custodian_name' => $vehicle->vehicle_custodian_name,
	            'vehicle_department_name' => $vehicle->vehicle_department_name
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
