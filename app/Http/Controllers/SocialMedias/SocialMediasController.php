<?php

namespace App\Http\Controllers\SocialMedias;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DB;
use App\Deputy;
use App\SocialMedia;

class SocialMediasController extends Controller
{
    public function getSocialMedias(){

        $id_deputies = $this->getDeputiesIdFromDb();

        $result = array_map(array($this, 'getArrayOfSocialMediasFromEachDeputy'), $id_deputies);

        $result = "Social medias inserted to database with success";
        // $result = $this->getArrayOfSocialMediasFromEachDeputy(18852);
        return $this->apiResult($result);


    }

    public function getDeputiesIdFromDb(){

        $id_deputies = DB::table('deputies')->select('id_deputy')->get()->pluck('id_deputy')->ToArray();

        return $id_deputies;

    }

    public function getArrayOfSocialMediasFromEachDeputy($id_deputy){


        $url = env('API_DEPUTIES_SOCIAL_MEDIAS');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url. $id_deputy);
        curl_setopt($curl, CURLOPT_FAILONERROR,1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
        $resultXml = curl_exec($curl);
        curl_close($curl);

        //convert xml to json
        $xmlLoaded = simplexml_load_string($resultXml, "SimpleXMLElement", LIBXML_NOCDATA);
        $resultJson = json_encode($xmlLoaded);
        $deputiesSocialMediasJson = json_decode($resultJson,TRUE);

        // the datas from endpoint aren't the same for every deputy
        if($deputiesSocialMediasJson["redesSociais"] != [] ){

            $return = array_map(array($this, 'sendSocialMediasToDb'), $deputiesSocialMediasJson["redesSociais"]["redeSocialDeputado"]);

        }

        $result = "Social medias inserted to database with success";

        return $this->apiResult($result);
    }

    public function sendSocialMediasToDb($deputiesSocialMediasJson){

        // php is not recognizing the array, it says deputiesSocialMediasJson is not an array
        $lengthOfArray = sizeof((array)$deputiesSocialMediasJson);

        if($lengthOfArray == 2){
            $nameOfSocialMedia = $deputiesSocialMediasJson["redeSocial"]["nome"];

            $datas = [
                "name" => $nameOfSocialMedia,
                "quantity" => 1,
            ];

            SocialMedia::create($datas);

            $result = "Social medias inserted to database with success";

            return $this->apiResult($result);

        }else if ($lengthOfArray ==3){
            // php is not recognizing the array, it says deputiesSocialMediasJson is not an array
            // so i needed to do some changes to manipulate it
            $deputiesSocialMediasJsonToArray = (array)$deputiesSocialMediasJson;
            $nameOfSocialMedia = array_splice($deputiesSocialMediasJsonToArray,1,1);

            $nameOfSocialMediaWithRightKey = $this->replace_key_function($nameOfSocialMedia, 'nome', 'name');
            $nameOfSocialMediaWithRightKey["quantity"] = 1;

            SocialMedia::create($nameOfSocialMediaWithRightKey);

            $result = "Social medias inserted to database with success";

            return $this->apiResult($result);
        }


    }

    public function replace_key_function($array, $key1, $key2){

        $keys = array_keys($array);
        $index = array_search($key1, $keys);

        if ($index !== false) {
            $keys[$index] = $key2;
            $array = array_combine($keys, $array);
        }

        return $array;
    }
}
