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

Route::group(['prefix' => 'api/v1', 'middleware'=> 'cors'], function(){
	Route::post('/auth', 'Auth\AuthController@userAuth');
	Route::get('/auth/me','Auth\AuthController@getAuthenticatedUser');
	Route::get('records/get/{filename}', [
	'as' => 'getentry', 'uses' => 'v1\VehicleUsageRecordController@get']);
	Route::get('dashboard/report', 'v1\DashboardController@generateMonthlyReport');
	Route::get('dashboard/report/vehicles', 'v1\DashboardController@vehiclesReport');
});	

Route::group(['prefix' => 'api/v1', 'middleware'=> ['cors','jwt.auth']], function(){

	Route::resource('custodians', 'v1\CustodianController');

	Route::resource('vehicles', 'v1\VehicleController');

	Route::resource('cards', 'v1\CardController');

	Route::resource('records', 'v1\VehicleUsageRecordController');

	Route::get('records/card/{id}', 'v1\VehicleUsageRecordController@getCardRecords');

	Route::post('records/reconcile', 'v1\VehicleUsageRecordController@reconcile');

	Route::get('dashboard/stats', 'v1\DashboardController@getStats');

	Route::get('dashboard/notifications', 'v1\DashboardController@getNotifications');

	Route::put('dashboard/notifications/{id}', 'v1\DashboardController@notificationUpdate');

	Route::get('dashboard/report/dates', 'v1\DashboardController@reportDates');

	Route::get('departments', function(){
		return App\Department::all();
	});
	Route::get('user-types', function(){
		return App\UserType::all();
	});
	Route::get('vehicle-types', function(){
		return App\VehicleType::all();
	});
	Route::get('notification-types', function(){
		return App\NotificationType::all();
	});
	Route::get('correction-status-types', function(){
		return App\RecordCorrectionStatus::all();
	});

});

Route::get('/', function(){
		return '<br><br><center><h1>If you see this, the server is working! <br>:)</h1></center>';
	});

