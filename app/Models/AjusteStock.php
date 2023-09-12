<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjusteStock extends Model
{
    use HasFactory;

    protected $table = "Ajuste_stock";// <-- El nombre personalizado

    public $timestamps = false;

    protected $primaryKey = "aju_cod";

    public $incrementing = false;

    protected $fillable = [
        'aju_pu',
        'aju_fec',
        'aju_ope',
        'aju_moti',
        'aju_arti',
        'aju_dep',
        'aju_cant',
        'aju_obs',
        'aju_est',
        'aju_afe',
        'aju_venci',
        'aju_cpc',
];

public function getDateFormat(){
    return 'Y-d-m H:m:s';
}
}