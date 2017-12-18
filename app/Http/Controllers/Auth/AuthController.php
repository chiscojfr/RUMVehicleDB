<?php

namespace App\Http\Controllers\Auth;

use App\Custodian;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use JWTAuth;
use App\UserType;
use App\Department;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /*
    |--------------------------------------------------------------------------
    | User login
    |--------------------------------------------------------------------------
    |
    | Param: user credentials 
    | Return: authentication token
    |
    */
    public function userAuth(Request $request){
        $credentials = $request->only('email','password');
        $token = null;

        try{
            if(!$token = JWTAuth::attempt($credentials)){ 
                return response()->json(['error' => 'invalid_credentials'], 404);
            }
        }catch(JWTException $ex){
            return response()->json(['error' => 'something_went_wrong'], 500);
        }

        return response()->json(compact('token'));

        
    }

    /*
    |--------------------------------------------------------------------------
    | Get auth user info 
    |--------------------------------------------------------------------------
    |
    | Return user info
    |
    */
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

        $data = [];
            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'position' => $user->position,
                'contact_number' => $user->contact_number,
                'employee_id' => $user->employee_id,
                'user_type_id' => $user->user_type_id,
                'department_id' => $user->department_id,
                'user_type_name' => $user->user_type_name,
                'user_department_name' => $user->user_department_name
            ];
        
        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('data'));
    }

}
