<?php

namespace App\Services\v1;
use App\Custodian;
use App\UserType;
use App\Department;
use JWTAuth;

class CustodiansService {

	public function createCustodian($request){

		return Custodian::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
            'position' => $request['position'],
            'contact_number' => $request['contact_number'],
            'employee_id' => $request['employee_id'],
            'user_type_id' => $request['user_type_id'],
            'department_id' => $request['department_id']
        ]);

        return response() -> json(['message' => 'The custodian has been created!'], 200);
	}

	public function updateCustodian($request, $id){

		$user = Custodian::find($id);
		if ($user == null){
			return response() -> json(['message' => 'Custodian not found!'], 404);
		}
		else{

			$user->fill($request->all());
			if(request()->has('password')){
				$user->password = bcrypt($request['password']);
			}
			
	        $user->save();

	        return response() -> json(['message' => 'The user has been updated!', 'data' =>$user], 200);
    	}
	}

	public function getCustodians($request){

		if( count( $request->all() ) == 0){
			return $this->filterCustodians(Custodian::paginate(10));
		}
		else{

			$custodian = new Custodian;

			$columns = [
				'department_id',
				'user_type_id',
				'is_active'
			];

			if(request()->has('is_active') ){
				if(request('is_active') == '1'){
					$custodian = $custodian->where('user_type_id', '!=', '4');
					return $this->filterCustodians($custodian->paginate(10));
				}
			}
			else{
				foreach ($columns as $column) {
				
					if(request()->has($column)){

						$custodian = $custodian->where($column, request($column));
					}
				}
			}

			foreach ($columns as $column) {
				
				if(request()->has($column)){

					$custodian = $custodian->where($column, request($column));
				}
			}
			
			return $this->filterCustodians($custodian->paginate(10));
		}
	}

	public function getCustodianInfo($id){

		$user = Custodian::find($id);
		if($user == null){
			$data = $user;
			if($data == null){
				return $data;
			}
        }
        else{

			$user_type_name = Usertype::find($user->user_type_id)->role;
	        $user->user_type_name =  $user_type_name;

	        $user_type_name_string = Usertype::find($user->user_type_id)->role_name;
	        $user->user_type_name_string = $user_type_name_string;

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
	                'user_type_name' => $user->user_type_name,
	                'user_type_name_string' => $user->user_type_name_string,
	                'user_department_name' => $user->user_department_name,
	                'user_type_id' => $user->user_type_id,
	                'department_id' => $user->department_id,
	            ];

			return $data;
		}
	}

	public function deleteCustodian($id){

		$user = Custodian::find($id);
		if ($user == null){
			return response() -> json(['message' => 'Custodian not found!'], 404);
		}
		else{

			$user = Custodian::destroy($id);

            return response() -> json(['message' => 'The custodian has been successfully deleted!'], 200);
		}
		
	}

	protected function filterCustodians($custodians) {
		$data = [];
		foreach ($custodians as $custodian){
			$user_type_name = Usertype::find($custodian->user_type_id)->role;
	        $custodian->user_type_name =  $user_type_name;

	        $user_type_name_string = Usertype::find($custodian->user_type_id)->role_name;
	        $custodian->user_type_name_string = $user_type_name_string;

	        $user_department_name = Department::find($custodian->department_id)->name;
	        $custodian->user_department_name =  $user_department_name;

			$entry = [
				'id' => $custodian->id,
				'name' => $custodian->name,
				'email' => $custodian->email,
				'position' => $custodian->position,
				'contact_number' => $custodian->contact_number,
				'employee_id' => $custodian->employee_id,
				'user_type_name' => $custodian->user_type_name,
				'user_type_name_string' => $custodian->user_type_name_string,
				'user_department_name' => $custodian->user_department_name,
				'user_type_id' => $custodian->user_type_id,
	            'department_id' => $custodian->department_id,
			];
			$data[] = $entry;
		}
		$data= [$custodians];
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
