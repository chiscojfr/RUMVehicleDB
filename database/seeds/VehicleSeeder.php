<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create(); 

        DB::table('vehicles')->insert([
            'make' => strtoupper('Toyota Test'),
            'vin' => '12341234123412341',
            'model' => strtoupper('Yaris'),
            'color' => strtoupper('White'),
            'year' => '2011',
            'type_id' => '17',
            'serial_number' => '2B7GB11X3TK105824',
            'property_number' => '137499',
            'marbete_date' => '2017-01-01',
            'inspection_date' => '2017-01-01',
            //'decomission_date' => '2017-01-01',
            'registration_id' => '3255524',
            'title_id' => '389509',
            'doors' => '4',
            'cylinders' => '4',
            'ACAA' => '35.00',
            'insurance' => '99.00',
            'purchase_price' => '12000',
            'inscription_date' => '2017-01-01',
            'license_plate' => 'HQP-342',
            'custodian_id' => '7',
            'department_id' => '4',
            'was_archived' => false,

        ]);

        DB::table('vehicles')->insert([
            'make' => strtoupper('Mazda Test'),
            'vin' => '32141234123412341',
            'model' => strtoupper('Protege'),
            'color' => strtoupper('Red'),
            'year' => '2013',
            'type_id' => '32',
            'serial_number' => '2B7GB11X3TK105824',
            'property_number' => '469432',
            'marbete_date' => '2017-01-01',
            'inspection_date' => '2017-01-01',
            //'decomission_date' => '2017-01-01',
            'registration_id' => '1265434',
            'title_id' => '12875345',
            'doors' => '4',
            'cylinders' => '4',
            'ACAA' => '35.00',
            'insurance' => '99.00',
            'purchase_price' => '12000',
            'inscription_date' => '2017-01-01',
            'license_plate' => 'HQP-313',
            'custodian_id' => '7',
            'department_id' => '4',
            'was_archived' => false,

        ]);

        for ($i=0; $i < 15; $i++) { 

            DB::table('vehicles')->insert([
                'make' => strtoupper('Honda Test Faker'),
                'vin' => '32141234123412341',
                'model' => strtoupper('Acord'),
                'color' => strtoupper($faker->colorName),
                'year' => $faker->year,
                'type_id' => '32',
                'serial_number' => '2B7GB11X3TK105824',
                'property_number' => '469432',
                'marbete_date' => '2017-01-01',
                'inspection_date' => '2017-01-01',
                //'decomission_date' => '2017-01-01',
                'registration_id' => '1265434',
                'title_id' => '12875345',
                'doors' => '4',
                'cylinders' => '4',
                'ACAA' => '35.00',
                'insurance' => '99.00',
                'purchase_price' => '12000',
                'inscription_date' => '2017-01-01',
                'license_plate' => 'HQP-313',
                'custodian_id' => '7',
                'department_id' => $faker->numberBetween($min = 1, $max = 95),
                'was_archived' => false,

            ]);
        }
        
    }
}

