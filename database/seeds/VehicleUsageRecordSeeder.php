<?php

use Illuminate\Database\Seeder;

class VehicleUsageRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //For testting purposes

        //This Record Fail the conciliation
        DB::table('vehicle_usage_records')->insert([
            'date' => '2016-12-14',
            'provider_number' => 'TOTAL MAYAGUEZ',
            'receipt_number' => '59934',
            'purchase_type' => 'Diesel',
            'total_liters' => '74.3',
            'total_receipt' => '54.75',
            'vehicle_mileage' => '33675.2',
            'comments' => 'This is a sample record!',
            'vehicle_id' => '2',
            'card_id' => '3',
            'custodian_id' => '6',
            'department_id' => '3',
        ]);

        //This Record Fail the conciliation
        DB::table('vehicle_usage_records')->insert([
            'date' => '2016-12-14',
            'provider_number' => 'TOTAL MAYAGUEZ',
            'receipt_number' => '59934',
            'purchase_type' => 'Diesel',
            'total_liters' => '78.3',
            'total_receipt' => '54.75',
            'vehicle_mileage' => '33675.2',
            'comments' => 'This is a sample record!',
            'vehicle_id' => '2',
            'card_id' => '3',
            'custodian_id' => '6',
            'department_id' => '3',
        ]);

        //This Record Pass the conciliation
        DB::table('vehicle_usage_records')->insert([
            'date' => '2016-12-14',
            'provider_number' => 'TOTAL MAYAGUEZ',
            'receipt_number' => '104295',
            'purchase_type' => 'Diesel',
            'total_liters' => '48.02',
            'total_receipt' => '32.51',
            'vehicle_mileage' => '33675.2',
            'comments' => 'This is a sample record!',
            'vehicle_id' => '2',
            'card_id' => '3',
            'custodian_id' => '6',
            'department_id' => '3',
        ]);
        //End testing
    }
}




           