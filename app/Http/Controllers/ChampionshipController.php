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

    public function sendTeams(Request $request){
        
        $championId = $request->championship;


        $championship = Championships::where('id', '=', $championId)->select('teams')->first();
        
        if($championship->teams !== null){

            $teamFormat = str_replace("]", "",  str_replace("[", "", $championship->teams));
        
            $teamExplode = explode(",",$teamFormat);

            if(count($teamExplode) === 8){
                return response("Limite de time para o campeonato atingido", 401);
            }else{
                foreach($teamExplode as $team){
                    $newTeam[] = str_replace('"',"",$team);
                }
                $newTeam[] = $request->name;

                Championships::where('id', '=', $championId)->update(['teams' => json_encode($newTeam)]);
            }

        }else{

            $newTeam[] = $request->name;
            Championships::where('id', '=', $championId)->update(['teams' => json_encode($newTeam)]);
        }

        return response('sucess', 200);
    }
}
