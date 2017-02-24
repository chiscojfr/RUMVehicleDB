<?php

namespace App\Services\v1;
use App\Custodian;
use App\UserType;
use App\Department;
use App\Card;
use App\Vehicle;
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
            'department_id' => $request['department_id']
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

	public function getCards(){
		return $this->filterCards(Card::all());
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
	            'card_custodian_name' => $card->card_custodian_name,
	            'card_department_name' => $card->card_department_name
			];

			return $data;
		}
	}

	public function getUserCards($id){

		$card = Card::where('custodian_id', $id)->get();
		return $this->filterCards($card);
		
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
	            'card_custodian_name' => $card->card_custodian_name,
	            'card_department_name' => $card->card_department_name
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
