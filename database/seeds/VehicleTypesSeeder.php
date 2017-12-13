<?php

use Illuminate\Database\Seeder;

class VehicleTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehicles_types =[
            'AMBULANCIA','ARRASTRE','ASTRO VAN','CAMION ARRASTRE','CAMION BARENA','CAMION BASURA','CAMION PLA','CAMION TUMBA','CARRO SOLAR','DIGGER','ELECT. CAR','GAS CAR','GAS GOLF CAR','GOLF GAS CAR','GRAND MARQ','GRUA','GUAGUA','GUAGUA 4X4','GUAGUA ESCOLAR','GUAGUA PASAJERO','HERRAMIENTA','HYBRID','JEEP','JEEP SAHARA','JET SKY','MINI VAN','MONTA CARGA','MOTORA','PICK UP','PICK UP 1 CAB','PICK UP 2 CAB','SEDAN-4','STATION W','TRACTOR','TRAILER','TROLLEY','VAN CARGA','VAN PASAJE','VAN PASAJERO'
        ];

        foreach ($vehicles_types as $vehicles_type) {

            DB::table('vehicles_types')->insert([
                'vehicle_type_name' => $vehicles_type
            ]);
            
        }
    }
}
