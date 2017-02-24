<?php

use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departments')->insert([
            'name' => 'Rectoria'
        ]);

        DB::table('departments')->insert([
            'name' => 'Matematicas'
        ]);

        DB::table('departments')->insert([
            'name' => 'Quimica'
        ]);
    }
}
