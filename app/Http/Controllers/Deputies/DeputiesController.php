<?php

namespace App\Http\Controllers\Deputies;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use App\Deputy;

class DeputiesController extends Controller
{
    public function getDeputies(){

        // $url = env('API_DEPUTIES');
        // $curl = curl_init($url);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // $response = curl_exec($curl);
        // $responseJson = json_decode($response, true);

        // $deputiesJson = $responseJson["list"];

        // $deputiesInserted = array_map(array($this, 'sendDeputiesToDb'), $deputiesJson);

        // $answer = "All deputies were send to database";

        // return $this->apiResult($answer);
        $client = new Client();

        $url = env('API_DEPUTIES');

        $response = $client->request('GET', $url);

        $responseJson = json_decode($response->getBody() , true);

        $deputiesJson = $responseJson["list"];

        $deputiesInserted = array_map(array($this, 'sendDeputiesToDb'), $deputiesJson);

        $answer = "All deputies were send to database";

        return $this->apiResult($answer);



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

    public function getThe5MoreReimbursementDeputiesPerMonth(){

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

}
