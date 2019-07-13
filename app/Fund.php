<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fund extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_deputy', 'month', 'expenses'
    ];

    public function getFiveMoreExpensiveDeputiesPerMonth($month){

        return $this->leftjoin('deputies' , 'deputies.id_deputy','funds.id_deputy')
                    ->select('funds.expenses as despesas', 'deputies.name as nome', 'funds.month as mês')
                    ->where('month',$month)
                    ->orderBy('funds.expenses', 'desc')
                    ->get()
                    ->take(5)
                    ->toArray();
    }
}
