<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'quantity',
    ];

    public function getSocialMediaRankingFromDb(){

        return $this->selectRaw('name as nome, count(name) as quantidade')
                    ->groupBy('name')
                    ->orderBy('quantidade' , 'desc')
                    ->get()
                    ->toArray();

    }
}
