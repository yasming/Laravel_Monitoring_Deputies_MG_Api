<?php

namespace App\Http\Controllers\Deputies;

use App\Http\Controllers\Controller;
use App\Fund;
use App\SocialMedia;

class DeputiesController extends Controller
{
    public function getTheFiveMoreReimbursementDeputiesPerMonth(){

        $funds = new Fund;

        $fiveMoreExpensiveDeputiesPerMonth = [];

        for($month = 1;  12 >= $month; $month++){

            $expensivesPerMonthAndNameOfDeputies = $funds->getFiveMoreExpensiveDeputiesPerMonth($month);

            array_push($fiveMoreExpensiveDeputiesPerMonth,$expensivesPerMonthAndNameOfDeputies);

        }

        return $this->ApiResult($fiveMoreExpensiveDeputiesPerMonth);


    }

    public function getRankingOfSocialMedia(){

        $socialMedia = new SocialMedia;

        $rankingOfSocialMidias = $socialMedia->getSocialMediaRankingFromDb();

        return $this->ApiResult($rankingOfSocialMidias);

    }

}
