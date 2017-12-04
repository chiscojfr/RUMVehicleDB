<?php

use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('vehicles')->insert([
            'make' => 'Toyota',
            'vin' => '1234566',
            'model' => 'Yaris',
            'color' => 'White',
            'year' => '2011',
            'type' => 'Regular',
            'serial_number' => '12345',
            'property_number' => '4432',
            'marbete_date' => '2017-01-01',
            'inspection_date' => '2017-01-01',
            'decomission_date' => '2017-01-01',
            'registration_id' => '1234',
            'title_id' => '12345',
            'doors' => '4',
            'cylinders' => '4',
            'ACAA' => '1234',
            'insurance' => 'Guardian',
            'purchase_price' => '12000',
            'inscription_date' => '2017-01-01',
            'license_plate' => 'HQP-342',
            'custodian_id' => '1',
            'department_id' => '1',

        ]);
    }
}

