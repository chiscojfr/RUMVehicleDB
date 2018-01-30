<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CustodianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $faker = Faker::create(); 
        //1
        DB::table('custodians')->insert([
            'name' => 'Admin',
            'email' => 'admin@upr.edu',
            'password' => bcrypt('abcd1234'),
            'position' => 'Master Admin',
            'contact_number' => '2020',
            'employee_id' => '123456',
            'user_type_id' => '1',
            'department_id' => '4'
        ]);
        //2
        DB::table('custodians')->insert([
            'name' => 'Jose Rodriguez',
            'email' => 'jose@upr.edu',
            'password' => bcrypt('abcd1234'),
            'position' => 'Web Developer',
            'contact_number' => '2020',
            'employee_id' => '123456',
            'user_type_id' => '1',
            'department_id' => '4'
        ]);
        //3
        DB::table('custodians')->insert([
            'name' => 'Diego Figueroa',
            'email' => 'diego@gmail.com',
            'password' => bcrypt('abcd1234'),
            'position' => 'Web Developer',
            'contact_number' => '2020',
            'employee_id' => '123456',
            'user_type_id' => '1',
            'department_id' => '4'
        ]);
        //4
        DB::table('custodians')->insert([
            'name' => 'Jan Vega',
            'email' => 'jan@gmail.com',
            'password' => bcrypt('abcd1234'),
            'position' => 'Web Developer',
            'contact_number' => '2020',
            'employee_id' => '123456',
            'user_type_id' => '1',
            'department_id' => '4'
        ]);
        //5
        DB::table('custodians')->insert([
            'name' => 'Test User Custodian',
            'email' => 'custodian@upr.edu',
            'password' => bcrypt('abcd1234'),
            'position' => 'Test User',
            'contact_number' => '2020',
            'employee_id' => '123456',
            'user_type_id' => '3',
            'department_id' => '5'
        ]);
        // //6
        // DB::table('custodians')->insert([
        //     'name' => 'Test User Custodian',
        //     'email' => 'custodian2@upr.edu',
        //     'password' => bcrypt('abcd1234'),
        //     'position' => 'Test User',
        //     'contact_number' => '2020',
        //     'employee_id' => '123456',
        //     'user_type_id' => '3',
        //     'department_id' => '6'
        // ]);
        //7
        DB::table('custodians')->insert([
            'name' => 'User Vehicle Admin',
            'email' => 'vehicle.admin@upr.edu',
            'password' => bcrypt('abcd1234'),
            'position' => 'Test User',
            'contact_number' => '2020',
            'employee_id' => '123456',
            'user_type_id' => '2',
            'department_id' => '2'
        ]);
        //8
        DB::table('custodians')->insert([
            'name' => 'Nancy Mendez',
            'email' => 'nancy.mendez1@upr.edu',
            'password' => bcrypt('abcd1234'),
            'position' => 'Test User',
            'contact_number' => '2020',
            'employee_id' => '123456',
            'user_type_id' => '1',
            'department_id' => '3'
        ]);
        //9
        DB::table('custodians')->insert([
            'name' => 'Jeannette Muñiz',
            'email' => 'jeannette.muniz@upr.edu',
            'password' => bcrypt('abcd1234'),
            'position' => 'Test User',
            'contact_number' => '2020',
            'employee_id' => '123456',
            'user_type_id' => '1',
            'department_id' => '3'
        ]);
        //10
        DB::table('custodians')->insert([
            'name' => 'Flavia Martinez',
            'email' => 'flavia.martinez@upr.edu',
            'password' => bcrypt('abcd1234'),
            'position' => 'Test User',
            'contact_number' => '2020',
            'employee_id' => '123456',
            'user_type_id' => '1',
            'department_id' => '94'
        ]);
        //11
        DB::table('custodians')->insert([
            'name' => 'Joannie Gonzalez',
            'email' => 'joannie.gonzalez1@upr.edu',
            'password' => bcrypt('abcd1234'),
            'position' => 'Test User',
            'contact_number' => '2020',
            'employee_id' => '123456',
            'user_type_id' => '3',
            'department_id' => '94'
        ]);
        //12
        DB::table('custodians')->insert([
            'name' => 'Tania Matos',
            'email' => 'tania.matos@upr.edu',
            'password' => bcrypt('abcd1234'),
            'position' => 'Test User',
            'contact_number' => '2020',
            'employee_id' => '123456',
            'user_type_id' => '3',
            'department_id' => '70'
        ]);


        // DB::table('custodians')->insert([
        //         'name' => 'Test Custodian Inactive',
        //         'email' => $faker->email,
        //         'password' => bcrypt($faker->password),
        //         'position' => 'Test User',
        //         'contact_number' => '2020',
        //         'employee_id' => '123456',
        //         'user_type_id' => '4',
        //         'department_id' => $faker->numberBetween($min = 1, $max = 95)
        // ]);

        // for ($i=0; $i < 10; $i++) { 
        //     DB::table('custodians')->insert([
        //         'name' => $faker->name,
        //         'email' => $faker->email,
        //         'password' => bcrypt($faker->password),
        //         'position' => 'Test User',
        //         'contact_number' => '2020',
        //         'employee_id' => '123456',
        //         'user_type_id' => '3',
        //         'department_id' => $faker->numberBetween($min = 1, $max = 95)
        //     ]);
        // }

    }
}
