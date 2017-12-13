<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTypeSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(CustodianSeeder::class);
        //$this->call(VehicleSeeder::class);
        $this->call(CardSeeder::class);
        $this->call(VehicleTypesSeeder::class);
        $this->call(VehicleUsageRecordSeeder::class);
        $this->call(NotificationTypeSeeder::class); 
        $this->call(NotificationStatusTypeSeeder::class);
    }
}
