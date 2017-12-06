<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\Department;
use App\UserType;

Route::group(['prefix' => 'api/v1', 'middleware'=> 'cors'], function(){
	Route::post('/auth', 'Auth\AuthController@userAuth');
	Route::get('/auth/me','Auth\AuthController@getAuthenticatedUser');
});	

Route::group(['prefix' => 'api/v1', 'middleware'=> ['cors','jwt.auth']], function(){

	Route::resource('custodians', 'v1\CustodianController');

	Route::get('vehicles/filter', 'v1\VehicleController@filter');

	Route::resource('vehicles', 'v1\VehicleController');

	Route::resource('cards/', 'v1\CardController');

	//Route::get('cards/filter', 'v1\CardController@filter');

	Route::get('records/filter', 'v1\VehicleUsageRecordController@filter');

	Route::resource('records', 'v1\VehicleUsageRecordController');

	Route::post('records/reconcile', 'v1\VehicleUsageRecordController@reconcile');

	Route::get('stats', 'v1\DashboardController@stats');

	Route::get('records/get/{filename}', [
	'as' => 'getentry', 'uses' => 'v1\VehicleUsageRecordController@get']);

	Route::get('departments', function(){
		return Department::all();
	});

	Route::get('user-types', function(){
		return UserType::all();
	});

});

Route::get('/', function(){
		return '<br><br><center><h1>If you see this, the server is working! <br>:)</h1></center>';
	});

