<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Deputy extends Model
{

    protected $fillable = [
        'name', 'id_deputy',
    ];

    public function getDeputiesId(){

        return $this->all()->pluck('id_deputy')->ToArray();

    }
}
