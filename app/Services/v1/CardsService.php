<?php

namespace App\Services\v1;
use App\Custodian;
use App\UserType;
use App\Department;
use App\Card;
use App\Vehicle;
use App\VehicleUsageRecord;
use JWTAuth;

class CardsService {

	public function createCard($request){

		return Card::create([
            'number' => $request['number'],
            'name' => $request['name'],
            'type' => $request['type'],
            'expiry' => $request['expiry'],
            'status' => $request['status'],
            'cardID' => $request['cardID'],
            'custodian_id' => $request['custodian_id'],
            'department_id' => $request['department_id'],
            //'auxiliary_custodian_id' => $request['auxiliary_custodian_id'],
            
        ]);

        return response() -> json(['message' => 'The card has been created!'], 200);
	}

	public function updateCard($request, $id){

		$card = Card::find($id);
		if ($card == null){
			return response() -> json(['message' => 'Card not found!'], 404);
		}
		else{

	        $card->fill($request->all());
	        $card->save();

	        return response() -> json(['message' => 'The card has been updated!', 'data' =>$card], 200);
    	}
	}

	public function getCards($request){

		if( count( $request->all() ) == 0){
			return $this->filterCards(Card::paginate(10));
		}
		else{
				$cards = new Card;
				$queries = [];

				$columns = [
					'type',
					'custodian_id',
					'department_id',
					'status'
				];

				foreach ($columns as $column) {
					if(request()->has($column)){
						
						$cards = $cards->where($column, request($column));
						$queries[$column] = request($column);
					}
				}
				
				return $this->filterCards($cards->paginate(10));
			}
	}

	public function getCardInfo($id){

		$card = Card::find($id);
		if($card == null){
			$data = $card;
			if($data == null){
				return $data;
			}
        }
        else{

			$card_department_name = Department::find($card->department_id)->name;
	        $card->card_department_name =  $card_department_name;

	        $card_custodian_name = Custodian::find($card->custodian_id)->name;
	        $card->card_custodian_name =  $card_custodian_name;


	        //$card_auxiliary_custodian_name = Custodian::find($card->auxiliary_custodian_id)->name;
	        //$card->card_auxiliary_custodian_name =  $card_auxiliary_custodian_name;

	        $data = [];

			$data = [
				'id' => $card->id,
				'number' => $card->number,
				'name' => $card->name,
				'type' => $card->type,
				'expiry' => $card->expiry,
				'status' => $card->status,
				'cardID' => $card->cardID,
				'custodian_id' => $card->custodian_id,
	            'department_id' => $card->department_id,
	            'auxiliary_custodian_id' => $card->auxiliary_custodian_id,
	            'card_custodian_name' => $card->card_custodian_name,
	            'card_department_name' => $card->card_department_name,
	            //'card_auxiliary_custodian_name' => $card->card_auxiliary_custodian_name
			];

			return $data;
		}
	}

	public function getUserCards($request, $id){

		if( count( $request->all() ) == 0){
			return $this->filterCards(Card::where('custodian_id', $id)->paginate(10));
		}
		else{
				$cards = Card::where('custodian_id', $id);
				$queries = [];

				$columns = [
					'type',
					'status'
				];

				foreach ($columns as $column) {
					if(request()->has($column)){
						
						$cards = $cards->where($column, request($column));
						$queries[$column] = request($column);
					}
				}
				
				return $this->filterCards($cards->paginate(10));
			}
		
	}

	public function deleteCard($id){

		$card = Card::find($id);
		if ($card == null){
			return response() -> json(['message' => 'Card not found!'], 404);
		}
		else{

			$card = Card::destroy($id);

            return response() -> json(['message' => 'The card has been successfully deleted!'], 200);
		}
		
	}

	protected function filterCards($cards) {
		$data = [];
		foreach ($cards as $card){

			$card_department_name = Department::find($card->department_id)->name;
	        $card->card_department_name =  $card_department_name;

	        $card_custodian_name = Custodian::find($card->custodian_id)->name;
	        $card->card_custodian_name =  $card_custodian_name;

	       // $card_auxiliary_custodian_name = Custodian::find($card->auxiliary_custodian_id)->name;
	       // $card->card_auxiliary_custodian_name =  $card_auxiliary_custodian_name;

			$entry = [
				'id' => $card->id,
				'number' => $card->number,
				'name' => $card->name,
				'type' => $card->type,
				'expiry' => $card->expiry,
				'status' => $card->status,
				'cardID' => $card->cardID,
				'custodian_id' => $card->custodian_id,
	            'department_id' => $card->department_id,
	            'auxiliary_custodian_id' => $card->auxiliary_custodian_id,
	            'card_custodian_name' => $card->card_custodian_name,
	            'card_department_name' => $card->card_department_name,
	            //'card_auxiliary_custodian_name' => $card->card_auxiliary_custodian_name
			];
			$data[] = $entry;
		}
		$data = [$cards];
		return $data;
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

	        $record_card = Card::find($record->card_id);
	        $record->record_card_name =  $record_card->name;
	        $record->record_card_number =  $record_card->number;

	        $picture = route('getentry', $record->filename);
	        $record->record_picture = $picture;

			$data = [
				'receipt_number' => $record->receipt_number,
				'date' => $record->date,
				'purchase_type' => $record->purchase_type,
				'total_liters' => $record->total_liters,
				'total_receipt' => $record->total_receipt,
				'vehicle_mileage' => $record->vehicle_mileage,
				'comments' => $record->comments,
				'record_department_name' => $record->record_department_name,
				'record_custodian_name' => $record->record_custodian_name,
				'record_card_name' => $record->record_card_name,
				'record_card_number' => $record->record_card_number,
				'record_picture' => $record->record_picture
			];

			return $data;
		}
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
