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
            'make' => 'Toyota-Test',
            'vin' => '12341234123412341',
            'model' => 'Yaris',
            'color' => 'White',
            'year' => '2011',
            'type_id' => '17',
            'serial_number' => '2B7GB11X3TK105824',
            'property_number' => '137499',
            'marbete_date' => '2017-01-01',
            'inspection_date' => '2017-01-01',
            'decomission_date' => '2017-01-01',
            'registration_id' => '3255524',
            'title_id' => '389509',
            'doors' => '4',
            'cylinders' => '4',
            'ACAA' => '1234',
            'insurance' => 'Guardian',
            'purchase_price' => '12000',
            'inscription_date' => '2017-01-01',
            'license_plate' => 'HQP-342',
            'custodian_id' => '7',
            'department_id' => '4',

        ]);

        DB::table('vehicles')->insert([
            'make' => 'Mazda-Test',
            'vin' => '32141234123412341',
            'model' => 'Protege',
            'color' => 'Red',
            'year' => '2013',
            'type_id' => '32',
            'serial_number' => '2B7GB11X3TK105824',
            'property_number' => '469432',
            'marbete_date' => '2017-01-01',
            'inspection_date' => '2017-01-01',
            'decomission_date' => '2017-01-01',
            'registration_id' => '1265434',
            'title_id' => '12875345',
            'doors' => '4',
            'cylinders' => '4',
            'ACAA' => '1234',
            'insurance' => 'Guardian',
            'purchase_price' => '12000',
            'inscription_date' => '2017-01-01',
            'license_plate' => 'HQP-313',
            'custodian_id' => '7',
            'department_id' => '4',

        ]);

        for ($i=0; $i < 15; $i++) { 

            DB::table('vehicles')->insert([
                'make' => 'Honda-Test',
                'vin' => '32141234123412341',
                'model' => 'Acord',
                'color' => $faker->colorName,
                'year' => $faker->year,
                'type_id' => '32',
                'serial_number' => '2B7GB11X3TK105824',
                'property_number' => '469432',
                'marbete_date' => '2017-01-01',
                'inspection_date' => '2017-01-01',
                'decomission_date' => '2017-01-01',
                'registration_id' => '1265434',
                'title_id' => '12875345',
                'doors' => '4',
                'cylinders' => '4',
                'ACAA' => '1234',
                'insurance' => 'Guardian',
                'purchase_price' => '12000',
                'inscription_date' => '2017-01-01',
                'license_plate' => 'HQP-313',
                'custodian_id' => '7',
                'department_id' => '4',

            ]);
        }
        
    }
}

