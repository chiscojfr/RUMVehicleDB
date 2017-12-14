<?php

use Illuminate\Database\Seeder;

class CustodianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('custodians')->insert([
            'name' => 'Admin',
            'email' => 'admin@upr.edu',
            'password' => bcrypt('123456'),
            'position' => 'Master Admin',
            'contact_number' => '00000',
            'employee_id' => '802000000',
            'user_type_id' => '1',
            'department_id' => '4'
        ]);

        DB::table('custodians')->insert([
            'name' => 'Jose F Rodriguez',
            'email' => 'jose@upr.edu',
            'password' => bcrypt('123456'),
            'position' => 'Web Developer',
            'contact_number' => '222554',
            'employee_id' => '802000000',
            'user_type_id' => '1',
            'department_id' => '4'
        ]);

        DB::table('custodians')->insert([
            'name' => 'Diego Figueroa',
            'email' => 'diego@gmail.com',
            'password' => bcrypt('123456'),
            'position' => 'Web Developer',
            'contact_number' => '222554',
            'employee_id' => '802000000',
            'user_type_id' => '1',
            'department_id' => '4'
        ]);

        DB::table('custodians')->insert([
            'name' => 'Jan Vega',
            'email' => 'jan@gmail.com',
            'password' => bcrypt('123456'),
            'position' => 'Web Developer',
            'contact_number' => '222554',
            'employee_id' => '802000000',
            'user_type_id' => '1',
            'department_id' => '4'
        ]);

        DB::table('custodians')->insert([
            'name' => 'Test-User-Custodian',
            'email' => 'custodian@upr.edu',
            'password' => bcrypt('123456'),
            'position' => 'Test User',
            'contact_number' => '00000',
            'employee_id' => '802000000',
            'user_type_id' => '2',
            'department_id' => '5'
        ]);

        DB::table('custodians')->insert([
            'name' => 'Test-User-Vehicle-Admin',
            'email' => 'vehicle.admin@upr.edu',
            'password' => bcrypt('123456'),
            'position' => 'Test User',
            'contact_number' => '00000',
            'employee_id' => '802000000',
            'user_type_id' => '2',
            'department_id' => '2'
        ]);
    }
}
