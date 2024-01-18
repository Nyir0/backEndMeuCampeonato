<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Championships;
use App\Models\History;

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

    public function simulateMatch($team1, $team2) {
        $goalsTeam1 = rand(0, 5);
        $goalsTeam2 = rand(0, 5);
    
        return [$team1, $goalsTeam1, $team2, $goalsTeam2];
    }

    // Função para simular uma fase do torneio e retornar os vencedores e perdedores
    public function simulatePhase($teams) {
        $vencedores = [];
        $resultados = [];

        for ($i = 0; $i < count($teams); $i += 2) {
            list($time1, $golsTime1, $time2, $golsTime2) = $this->simulateMatch($teams[$i], $teams[$i + 1]);

            // Adicionar resultado ao array
            $resultados[] = [
                'time1' => $time1,
                'golsTime1' => $golsTime1,
                'time2' => $time2,
                'golsTime2' => $golsTime2,
            ];

            // Determinar o vencedor com base nos gols marcados
            $vencedor = $golsTime1 > $golsTime2 ? $time1 : $time2;
            $vencedores[] = $vencedor;
        }

        return [$vencedores, $resultados];
    }

    public function simulation(Request $request){
        $champ = Championships::where('id', '=', $request->championship)->first();

        $teamFormat = str_replace("]", "",  str_replace("[", "", $champ->teams));
        
        $teamExplode = explode(",",$teamFormat);
        if(count($teamExplode)< 8){
            return response('Numero de times insuficiente', 401);
        }

        // Início do Campeonato
        $equipesRestantes = $teamExplode;
        $allResults = [];

        // Quartas de Final
        list($quartasFinalVencedores, $quartasFinalResults) = $this->simulatePhase($equipesRestantes);
        $allResults["Quatas de final"] = $quartasFinalResults;

        // Semifinais
        list($semifinaisVencedores, $semifinaisResults) = $this->simulatePhase($quartasFinalVencedores);
        $allResults["Semi-final"] = $semifinaisResults;

        // Disputa pelo Terceiro Lugar
        list($terceiroLugar, $terceiroLugarResults) = $this->simulatePhase(array_values(array_diff($quartasFinalVencedores, $semifinaisVencedores)));
        $allResults["Terceiro lugar"] = $terceiroLugarResults;

        // Final
        list($campeao, $finalResults) = $this->simulatePhase($semifinaisVencedores);
        $allResults["Final"] = $finalResults;

        // Encontrar o time que perdeu na final como vice-campeão
        if($semifinaisVencedores[0] === $campeao[0]){
            $vice = $semifinaisVencedores[1];
        }else{
            $vice = $semifinaisVencedores[0];
        }
        
        $campeao = $campeao[0];

        $history = new history;
        $history->championship = $request->championship;
        $history->matches = json_encode(['AllResults' => $allResults, 'Champion' => $campeao, 'RunnerUp' => $vice]);
        $history->save();


        // Retornar resultados de todas as fases
        return ['AllResults' => $allResults, 'Champion' => $campeao, 'RunnerUp' => $vice, "id" => $history->id];
    }

    public function history(){
        $history = History::latest()->take(5)->get();

        $result = [];

        if(empty($history)){
            return 'Sem historico';
        };

        foreach($history as $index){
            $result[] = $index->matches;
        }

        return $result;
    }
}
