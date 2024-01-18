<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Championships;

class ChampionshipController extends Controller
{
    public function getAll(){

        $list = [];

        $championships = Championships::select('id', 'name')->get();
        foreach($championships as $champ){
            $list[] = $champ;
        }
        return json_encode($list);
    }

    public function getTeams(Request $request){
        
        $id = $request->id;
        
        $championships = Championships::where('id', '=', $id)->select('teams')->first();

        $list = [];

        if(empty($championships)){
            return "Campeonato nao existe";
        }

        $teamFormat = str_replace("]", "",  str_replace("[", "", $championships->teams));
        
        $teamExplode = explode(",",$teamFormat);

        foreach($teamExplode as $Team){
            $list[]["name"] = $Team;
        }

        return json_encode($list);
    } 
}
