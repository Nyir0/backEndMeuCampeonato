<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Championships;

class DatabaseChampionship extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Championships::truncate();
        
        $dados = ['championship1', 'championship2','championship3'];

        $times = ['Santos', 'Corinthians', 'Sao Paulo', 'Vasco', 'Palmeiras', 'Flamengo', 'Gremio', 'Cruzeiro'];

        for($c = 0; $c < count($dados); $c++){
            if($c === 0){
                $json = json_encode($times);
                Championships::create(['name' => $dados[$c], 'teams' => $json]);    
            }else{
                Championships::create(['name' => $dados[$c]]);
            }
        }
    }
}
