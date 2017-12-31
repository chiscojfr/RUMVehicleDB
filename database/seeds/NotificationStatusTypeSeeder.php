<?php

use Illuminate\Database\Seeder;

class NotificationStatusTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('correction_status_types')->insert([
            'status_type_name' => 'Pending for custodian justification'
        ]);

        DB::table('correction_status_types')->insert([
            'status_type_name' => 'Pending for approving'
        ]);

        DB::table('correction_status_types')->insert([
            'status_type_name' => 'Approved'
        ]);

        DB::table('correction_status_types')->insert([
            'status_type_name' => 'Not Approved'
        ]);

    }
}
