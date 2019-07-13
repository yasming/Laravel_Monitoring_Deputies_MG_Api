<?php

use Illuminate\Database\Seeder;
use App\Deputy;

class DeputySeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $url = env('API_DEPUTIES');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
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
}
