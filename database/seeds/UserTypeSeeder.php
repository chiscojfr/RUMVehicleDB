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
            'role' => 'admin'
        ]);

        DB::table('user_types')->insert([
            'role' => 'vehicle_admin'
        ]);

        DB::table('user_types')->insert([
            'role' => 'custodian'
        ]);

        DB::table('user_types')->insert([
            'role' => 'auxiliary_custodian'
        ]);

        DB::table('user_types')->insert([
            'role' => 'inactive'
        ]);
    }
}
