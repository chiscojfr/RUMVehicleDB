<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create(); 

        DB::table('cards')->insert([
            'name' => 'test-card-1',
            'number' => '12341234123412341',
            'type' => 'Regular',
            'expiry' => '2018-01-01',
            'status' => 'Active',
            'cardID' => '123456',
            'custodian_id' => '5',
            'department_id' => '5'
        ]);

        DB::table('cards')->insert([
            'name' => 'test-card-2',
            'number' => '34341234123412332',
            'type' => 'Premium',
            'expiry' => '2018-01-01',
            'status' => 'Active',
            'cardID' => '432156',
            'custodian_id' => '5',
            'department_id' => '5'
        ]);

        DB::table('cards')->insert([
            'name' => 'test-card-3',
            'number' => '34341234123412337',
            'type' => 'Diesel',
            'expiry' => '2018-01-01',
            'status' => 'Active',
            'cardID' => '546756',
            'custodian_id' => '6',
            'department_id' => '6'
        ]);

        DB::table('cards')->insert([
            'name' => 'test-card-4',
            'number' => '44341234123412332',
            'type' => 'Spare',
            'expiry' => '2018-01-01',
            'status' => 'Inactive',
            'cardID' => '432156',
            'custodian_id' => '4',
            'department_id' => '4'
        ]);

        for ($i=0; $i < 10; $i++) { 

            DB::table('cards')->insert([
                'name' => 'test-card-faker',
                'number' => '44341234123412332',
                'type' => 'Spare',
                'expiry' => '2018-01-01',
                'status' => 'Active',
                'cardID' => '432156',
                'custodian_id' => $faker->numberBetween($min = 4, $max = 6),
                'department_id' => '4'
            ]);
        }

    }
}