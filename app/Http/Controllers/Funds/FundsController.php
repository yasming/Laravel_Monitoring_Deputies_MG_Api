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

        array_map(array($this, 'getAllExpensesPerMonth'), $id_deputies);

        $result = "All expenses were send to database";

        return $this->apiResult($result);

    }

    public function getDeputiesIdFromDb(){

        $id_deputies = DB::table('deputies')->select('id_deputy')->get()->pluck('id_deputy')->ToArray();

        return $id_deputies;

    }

    public function getAllExpensesPerMonth($id_deputy){

        for($month = 1; 12 >= $month ; $month++){

            $url = env('API_FUNDS');
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url. $id_deputy. "/".env("YEAR")."/". $month);
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

        $result = "All expenses  were send to database";

        return $this->apiResult($result);

    }

    public function insertExpensesIntoDb($expensesResultJson, $month){

        $id_deputy = $expensesResultJson["resumoVerba"][0]["idDeputado"];

        $totalValueOfExpenses = 0;

        for($expensive = 0;  $expensive < sizeof($expensesResultJson["resumoVerba"]); $expensive++){

            $value = $expensesResultJson["resumoVerba"][$expensive]["valor"];
            (double)$valueWithDot = str_replace(",", "." , $value);
            $totalValueOfExpenses = $totalValueOfExpenses + $valueWithDot;
        }

        $datas = [
            'id_deputy' => $id_deputy,
            'month' => $month,
            'expenses' => $totalValueOfExpenses
        ];

        DB::table('funds')->insert($datas);

        $result = "Expenses added";
        return $this->apiResult($result);

    }

}
