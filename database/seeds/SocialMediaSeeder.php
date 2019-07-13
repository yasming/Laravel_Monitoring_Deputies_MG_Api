<?php

use Illuminate\Database\Seeder;
use App\Deputy;
use App\SocialMedia;

class SocialMediaSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $deputy = new Deputy;

        $id_deputies = $deputy->getDeputiesId();

        array_map(array($this, 'getArrayOfSocialMediasFromEachDeputy'), $id_deputies);

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

            array_map(array($this, 'sendSocialMediasToDb'), $deputiesSocialMediasJson["redesSociais"]["redeSocialDeputado"]);

        }

        $result = "Social medias inserted to database with success";

        return $result;
    }

    public function sendSocialMediasToDb($deputiesSocialMediasJson){

        // php is not recognizing the array, it says deputiesSocialMediasJson is not an array
        $lengthOfArray = sizeof((array)$deputiesSocialMediasJson);

        if($lengthOfArray == 2){
            $nameOfSocialMedia = $deputiesSocialMediasJson["redeSocial"]["nome"];

            $datas = [
                "name" => $nameOfSocialMedia,
            ];

            SocialMedia::create($datas);

            $result = "Social medias inserted to database with success";

            return $result;

        }else if ($lengthOfArray ==3){
            // php is not recognizing the array, it says deputiesSocialMediasJson is not an array
            // so i needed to do some changes to manipulate it
            $deputiesSocialMediasJsonToArray = (array)$deputiesSocialMediasJson;
            $nameOfSocialMedia = array_splice($deputiesSocialMediasJsonToArray,1,1);

            $nameOfSocialMediaWithRightKey = $this->replace_key_function($nameOfSocialMedia, 'nome', 'name');

            SocialMedia::create($nameOfSocialMediaWithRightKey);

            $result = "Social medias inserted to database with success";

            return $result;
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
