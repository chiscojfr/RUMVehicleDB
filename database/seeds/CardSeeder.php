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
            'name' => 'test',
            'type' => 'Regular',
            'expiry' => '2018-01-01',
            'status' => 'online',
            'cardID' => '1234',
            'custodian_id' => '1',
            'department_id' => '1'
        ]);
    }
}