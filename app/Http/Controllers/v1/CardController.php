<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Custodian;
use App\UserType;
use App\Card;
use App\Services\v1\CustodiansService;
use App\Services\v1\CardsService;

class CardController extends Controller
{
	public function __construct(CardsService $service){
        $this->cards = $service;
    }

    /*
    |--------------------------------------------------------------------------
    | Get cards info depending user role.
    |--------------------------------------------------------------------------
    |
    | Param: Filter parameters
    | Return: Cards info
    |
    */
    public function index(Request $request)
    {
        $user = $this->cards->getAuthenticatedUser();
        if($user->user_type_name == 'admin'){

            $data = $this->cards->getCards($request);

            return response()->json(['data' => $data], 200);
        }
        else {

             $data = $this->cards->getUserCards($user->id);
             if(empty($data)){
             	return response()->json(['message' => 'Error: This custodian has not have cards.'], 404);
             }
             return response()->json(['data' => $data], 200);
        }
        
    }

    /*
    |--------------------------------------------------------------------------
    | Create new card.
    |--------------------------------------------------------------------------
    |
    | Param: New card request
    | Return: New card info
    |
    */
    public function store(Request $request)
    {   
        $user = $this->cards->getAuthenticatedUser();
        if($user->user_type_name == 'admin'){

            $card = $this->cards->createCard($request);

            return response()->json(['message' => 'Card created successfully!',$card], 201);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can create new cards.'], 401);
        }

    }

    /*
    |--------------------------------------------------------------------------
    | Get single card info
    |--------------------------------------------------------------------------
    |
    | Param: card_id
    | Return: Card info
    |
    */
    public function show($id)
    {   
        $user = $this->cards->getAuthenticatedUser();
        $data = $this->cards->getCardInfo($id);

        if($data == null ){
            return response()->json(['message' => 'Error: Card not found!'],404);
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
    | Update card info 
    |--------------------------------------------------------------------------
    |
    | Param: Request, card_id 
    | Reutrn: Updated card info
    |
    */
    public function update(Request $request, $id)
    {   
        $user = $this->cards->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){
            return $this->cards->updateCard($request, $id);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can update cards.'], 401);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete card  
    |--------------------------------------------------------------------------
    |
    | Param: card_id 
    | Return: sucessful message
    |
    */
    public function destroy($id)
    {   
        $user = $this->cards->getAuthenticatedUser();

        if($user->user_type_name == 'admin'){
            return $this->cards->deleteCard($id);
        }
        else {
            return response()->json(['message' => 'Error: Only Admin can delete cards.'], 401);
        }

    }


}