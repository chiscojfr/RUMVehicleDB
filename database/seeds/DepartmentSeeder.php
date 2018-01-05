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
        $departments =[
            'ACT. ATLETICAS','ADM. EMPRESAS','ADMINISTRACION','ARTES Y CIENCIAS','ASUNTOS ACADEMICOS','BIBLIOTECA','BIOLOGIA','BOY SCOUT','C. I. D.','CAMPOS Y CARRETERA','CARIBEAN CORAL INST.','CARPINTERIA','CENTRO COMPUTOS','CIENCIAS AGRICOLAS','CIENCIAS MARINAS','CIENCIAS SOBRE RUEDAS','CIENCIAS Y TECNOLOGIA','CIRCUITO CERRADO','COLISEO','COLOCACIONES','COMPLEJO NATATORIO','CONSTRUCCION','CORREO','DEC. ADMINISTRACION','DEC. ESTUDIANTES','DECANO ASOCIADO','DECANO ASOCIADO OFFICE','DEPTO DE LLAVES','DIVISION EXTENCION','EDIFICIOS Y TERRENOS','ELECTRICIDAD','ELECTRONICA','ENFERMERIA','ENLACE CON PERSONAL','EST. EXP. COROZAL','ESTUDIANTES','FACULTAD C. AGRICOLAS','FINANZAS','FINCA ALZAMORA','FINCA MONTAÑA','GEOLOGIA','GUARDIA UNIVERSITARIA','IMPRESOS','INDUSTRIAS PECUARIAS','ING. & AGRIMENSURA','ING. AGRICOLA','ING. CIVIL','ING. ELECTRICA','ING. ELECTRICA & COMP.','ING. ELECTRICA (MRI)','ING. GENERAL','ING. MECANICA','INGENIERIA','INGENIERIA CIVIL','INGENIERIA GENERAL','INGENIERIA MECANICA','INGENIERIA QUIMICA','LAJAS','LIMPIEZA','MATEMATICAS','OFIC. RECURSOS HUMANOS','OFICINA DE PRENSA','OFICINA DEL DIRECTOR','OFICINA ING. ELECTRICO','OFICINA PROPIEDAD','OFICINA SEGURIDAD','PLANTA FISICA','PLOMERIA','PRE ESCOLAR','PRENSA','PROG MOVIMIENTO FUERTE','PROG. CARICOO & NSF','PROG. CARICOOS & NSF','PROGRAMA GLOBE','PROGRAMA MBRS','PROPIEDAD','PUBLICACIONES A & C','QUIMICA','RECTORIA','RED SISMICA','REFRIGERACION','REGISTRADURIA','SALUD Y SEGURIDAD','SEA GRANT','SECCION DE INGENIERIA','SERV. ESPECIALES','SERVICIOS AUXILIARES','SERVICIOS ESPECIALES','SERVICIOS MEDICOS','SOLDADURA','TALLER CARROS GOLF','TALLER MECANICA','TELEFONICA','TRANSPORTACION','VAQUERIA DE LAJAS'
        ];

        foreach ($departments as $department) {

            DB::table('departments')->insert([
                'name' => $department
            ]);
            
        }

    }
}


