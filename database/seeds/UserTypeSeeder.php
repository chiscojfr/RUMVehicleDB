<?php

use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_types')->insert([
            'role' => 'admin',
            'role_name' => 'Administrator'
        ]);

        DB::table('user_types')->insert([
            'role' => 'vehicle_admin',
            'role_name' => 'Vehicle Administrator'
        ]);

        DB::table('user_types')->insert([
            'role' => 'custodian',
            'role_name' => 'Custodian'
        ]);

        DB::table('user_types')->insert([
            'role' => 'auxiliary_custodian',
            'role_name' => 'Auxiliary Custodian'
        ]);

        DB::table('user_types')->insert([
            'role' => 'inactive',
            'role_name' => 'Inactive'
        ]);
    }
}
