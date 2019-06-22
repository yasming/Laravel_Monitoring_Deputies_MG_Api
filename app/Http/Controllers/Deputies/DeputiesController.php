<?php

namespace App\Http\Controllers\Deputies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Deputy;

class DeputiesController extends Controller
{
    public function getDeputies(){

        $url = env('API_DEPUTIES');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://dadosabertos.almg.gov.br/ws/deputados/em_exercicio");
        curl_setopt($curl, CURLOPT_FAILONERROR,1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        $resultXml = curl_exec($curl);
        curl_close($curl);

        //convert xml to json
        $xmlLoaded = simplexml_load_string($resultXml, "SimpleXMLElement", LIBXML_NOCDATA);
        $resultJson = json_encode($xmlLoaded);
        $deputiesResultJson = json_decode($resultJson,TRUE);
        $deputiesJson = $deputiesResultJson["deputado"];

        array_map(array($this, 'sendDeputiesToDb'), $deputiesJson);

        $result = "All deputies were send to database";

        return $this->apiResult($result);

    }

    public function sendDeputiesToDb($deputy){

        $data = [
            "id_deputy" => $deputy['id'],
            "name" => $deputy['nome']
        ];

        Deputy::create($data);
        $result = "Deputy inserted";

        return $result;
    }

    public function getTheFiveMoreReimbursementDeputiesPerMonth(){

        $fiveMoreExpensiveDeputiesPerMonth = [];

        for($month = 1;  12 >= $month; $month++){

            $ExpensivesPerMonthAndNameOfDeputies = DB::table('funds')->leftjoin('deputies' , 'deputies.id_deputy','funds.id_deputy')
                                                                     ->select('funds.expenses as despesas', 'deputies.name as nome', 'funds.month as mês')
                                                                     ->where('month',$month)
                                                                     ->orderBy('funds.expenses', 'desc')
                                                                     ->get()
                                                                     ->toArray();

            $fiveMoreExpensiveDeputies = array_slice($ExpensivesPerMonthAndNameOfDeputies,0,5);
            array_push($fiveMoreExpensiveDeputiesPerMonth,$fiveMoreExpensiveDeputies);

        }
        return $this->ApiResult($fiveMoreExpensiveDeputiesPerMonth);


    }

    public function getRankingOfSocialMedia(){

        $rankingOfSocialMidias = DB::table('social_media')->selectRaw('name as nome, sum(quantity) as quantidade')
                                                          ->groupBy('name')
                                                          ->orderBy('quantidade' , 'desc')
                                                          ->get()
                                                          ->toArray();

        return $this->ApiResult($rankingOfSocialMidias);

    }

}
