<?php

use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cards')->insert([
            'name' => 'test-1',
            'number' => '4444-4444-3333-2222',
            'type' => 'Regular',
            'expiry' => '2018-01-01',
            'status' => 'Active',
            'cardID' => '1234',
            'custodian_id' => '1',
            'department_id' => '4'
        ]);

        DB::table('cards')->insert([
            'name' => 'test-2',
            'number' => '1111-4444-3333-2222',
            'type' => 'Premium',
            'expiry' => '2018-01-01',
            'status' => 'Active',
            'cardID' => '4321',
            'custodian_id' => '1',
            'department_id' => '4'
        ]);

        DB::table('cards')->insert([
            'name' => 'test-3',
            'number' => '8888-4444-3333-2222',
            'type' => 'Diesel',
            'expiry' => '2018-01-01',
            'status' => 'Active',
            'cardID' => '5467',
            'custodian_id' => '3',
            'department_id' => '4'
        ]);

        DB::table('cards')->insert([
            'name' => 'test-4',
            'number' => '9999-4444-3333-2222',
            'type' => 'Spare',
            'expiry' => '2018-01-01',
            'status' => 'Inactive',
            'cardID' => '4321',
            'custodian_id' => '4',
            'department_id' => '4'
        ]);

    }
}