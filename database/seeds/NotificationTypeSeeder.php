<?php

use Illuminate\Database\Seeder;

class NotificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notification_types')->insert([
            'notification_type_name' => 'Not reconcile by: Data Entry Error'
        ]);

        DB::table('notification_types')->insert([
            'notification_type_name' => 'Not reconcile by: Cutoff Date'
        ]);

        DB::table('notification_types')->insert([
            'notification_type_name' => 'Record not found'
        ]);

        DB::table('notification_types')->insert([
            'notification_type_name' => 'Correction approved!'
        ]);

        DB::table('notification_types')->insert([
            'notification_type_name' => 'Correction not approved!'
        ]);
    }
}
