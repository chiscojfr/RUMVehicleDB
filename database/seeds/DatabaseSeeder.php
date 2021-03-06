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
        $this->call(VehicleSeeder::class);
        $this->call(CardSeeder::class);
    }
}
