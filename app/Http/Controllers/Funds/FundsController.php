<?php

namespace App\Http\Controllers\Funds;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use App\Deputy;
use Vyuldashev\XmlToArray\XmlToArray;

class FundsController extends Controller
{
    public function getDeputiesExpenses(){

        $id_deputies = $this->getDeputiesIdFromDb();

        $result = array_map(array($this, 'getAllExpenses'), $id_deputies);

        $answer = "All expenses  were send to database";

        return $this->apiResult($answer);

    }

    public function getDeputiesIdFromDb(){

        $id_deputies = DB::table('deputies')->select('id_deputy')->get()->pluck('id_deputy')->ToArray();

        return $id_deputies;

    }

    public function getAllExpenses($id_deputies){

        for($month = 1; 12 >= $month ; $month++){

            $url = env('API_FUNDS');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url. $id_deputies. "/2017/". $month);
            curl_setopt($curl, CURLOPT_FAILONERROR,1);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 15);
            $resultXml = curl_exec($curl);
            curl_close($curl);

            //convert xml to json
            $xmlLoaded = simplexml_load_string($resultXml, "SimpleXMLElement", LIBXML_NOCDATA);
            $resultJson = json_encode($xmlLoaded);
            $expensesResultJson = json_decode($resultJson,TRUE);

            if($expensesResultJson != null){
                $this->insertExpensesIntoDb($expensesResultJson, $month);
            }

        }

        $result = "Expenses added";
        return $this->apiResult($result);

    }

    public function insertExpensesIntoDb($expensesResultJson, $month){

        $id_deputy = $expensesResultJson["resumoVerba"][0]["idDeputado"];

        $totalValue = 0;

        for($despesa = 0;  $despesa < sizeof($expensesResultJson["resumoVerba"]); $despesa++){

            $value = $expensesResultJson["resumoVerba"][$despesa]["valor"];
            (double)$valueWithDot = str_replace(",", "." , $value);
            $totalValue = $totalValue + $valueWithDot;
        }

        $datas = [
            'id_deputy' => $id_deputy,
            'month' => $mes,
            'expenses' => $totalValue
        ];

        DB::table('funds')->insert($datas);

        $result = "Expenses added";
        return $this->apiResult($result);

    }

}
