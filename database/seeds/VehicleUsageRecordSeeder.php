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
        DB::table('vehicle_usage_records')->insert([
            'date' => '2017-12-01',
            'provider_number' => 'TOTAL MAYAGUEZ',
            'receipt_number' => '59932',
            'purchase_type' => 'Regular',
            'total_liters' => '45.5',
            'total_receipt' => '20.50',
            'vehicle_mileage' => '125675',
            'comments' => 'This is a sample record!',
            'vehicle_id' => '1',
            'card_id' => '1',
            'custodian_id' => '1',
        ]);

        DB::table('vehicle_usage_records')->insert([
            'date' => '2017-12-02',
            'provider_number' => 'TOTAL MAYAGUEZ',
            'receipt_number' => '59933',
            'purchase_type' => 'Premium',
            'total_liters' => '44.3',
            'total_receipt' => '24.75',
            'vehicle_mileage' => '33675.2',
            'comments' => 'This is a sample record!',
            'vehicle_id' => '2',
            'card_id' => '2',
            'custodian_id' => '2',
        ]);

        DB::table('vehicle_usage_records')->insert([
            'date' => '2017-12-05',
            'provider_number' => 'TOTAL MAYAGUEZ',
            'receipt_number' => '59934',
            'purchase_type' => 'Diesel',
            'total_liters' => '74.3',
            'total_receipt' => '54.75',
            'vehicle_mileage' => '33675.2',
            'comments' => 'This is a sample record!',
            'vehicle_id' => '2',
            'card_id' => '2',
            'custodian_id' => '2',
        ]);
    }
}




           