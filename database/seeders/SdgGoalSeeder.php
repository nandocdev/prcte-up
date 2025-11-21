<?php

namespace Database\Seeders;

use App\Models\SdgGoal;
use Illuminate\Database\Seeder;

class SdgGoalSeeder extends Seeder
{
    public function run(): void
    {
        $goals = [
         ['code' => 'ODS01', 'color' =>  "#e5243b", 'name' => 'Fin de la Pobreza', 'description' => 'Poner fin a la pobreza en todas sus formas y en todo el mundo.'],
         ['code' => 'ODS02', 'color' =>  "#dda63a", 'name' => 'Hambre Cero', 'description' => 'Poner fin al hambre, lograr la seguridad alimentaria y promover la agricultura sostenible.'],
         ['code' => 'ODS03', 'color' =>  "#4c9f38", 'name' => 'Salud y Bienestar', 'description' => 'Garantizar una vida sana y promover el bienestar en todas las edades.'],
         ['code' => 'ODS04', 'color' =>  "#c5192d", 'name' => 'Educación de Calidad', 'description' => 'Garantizar una educación inclusiva, equitativa y de calidad.'],
         ['code' => 'ODS05', 'color' =>  "#ff3a21", 'name' => 'Igualdad de Género', 'description' => 'Lograr la igualdad entre los géneros y empoderar a todas las mujeres y las niñas.'],
         ['code' => 'ODS06', 'color' =>  "#26bde2", 'name' => 'Agua Limpia y Saneamiento', 'description' => 'Garantizar la disponibilidad y la gestión sostenible del agua y el saneamiento.'],
         ['code' => 'ODS07', 'color' =>  "#fcc30b", 'name' => 'Energía Asequible y No Contaminante', 'description' => 'Garantizar el acceso a una energía asequible, fiable, sostenible y moderna.'],
         ['code' => 'ODS08', 'color' =>  "#a21942", 'name' => 'Trabajo Decente y Crecimiento Económico', 'description' => 'Promover el crecimiento económico sostenido, el empleo pleno y productivo.'],
         ['code' => 'ODS09', 'color' =>  "#fd6925", 'name' => 'Industria, Innovación e Infraestructura', 'description' => 'Construir infraestructuras resilientes y fomentar la innovación.'],
         ['code' => 'ODS10', 'color' =>  "#dd1367", 'name' => 'Reducción de las Desigualdades', 'description' => 'Reducir la desigualdad en y entre los países.'],
         ['code' => 'ODS11', 'color' =>  "#fd9d24", 'name' => 'Ciudades y Comunidades Sostenibles', 'description' => 'Lograr que las ciudades sean inclusivas, seguras y resilientes.'],
         ['code' => 'ODS12', 'color' =>  "#bf8b2e", 'name' => 'Producción y Consumo Responsables', 'description' => 'Garantizar modalidades de consumo y producción sostenibles.'],
         ['code' => 'ODS13', 'color' =>  "#3f7e44", 'name' => 'Acción por el Clima', 'description' => 'Adoptar medidas urgentes para combatir el cambio climático.'],
         ['code' => 'ODS14', 'color' =>  "#0a97d9", 'name' => 'Vida Submarina', 'description' => 'Conservar y utilizar sosteniblemente los océanos, los mares y los recursos marinos.'],
         ['code' => 'ODS15', 'color' =>  "#56c02b", 'name' => 'Vida de Ecosistemas Terrestres', 'description' => 'Gestionar sosteniblemente los bosques, luchar contra la desertificación y detener la pérdida de biodiversidad.'],
         ['code' => 'ODS16', 'color' =>  "#00689d", 'name' => 'Paz, Justicia e Instituciones Sólidas', 'description' => 'Promover sociedades pacíficas e inclusivas y fortalecer las instituciones.'],
         ['code' => 'ODS17', 'color' =>  "#19486a", 'name' => 'Alianzas para Lograr los Objetivos', 'description' => 'Revitalizar la Alianza Mundial para el Desarrollo Sostenible.'],
        ];

        foreach ($goals as $goal) {
            SdgGoal::updateOrCreate(
                ['code' => $goal['code']],
                ['name' => $goal['name'], 'description' => $goal['description']]
            );
        }
    }
}

