<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = "ventas";// <-- El nombre personalizado

    protected $primaryKey = "id_ven";

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'id_ven',
        'v_pu',
        'fec',
        'tipcom',
        'compro',
        'timb',
        'cond',
        'venci',
        'clien',
        'tipven',
        'mone',
        'monto',
        'saldo',
        'venes',
        'sel',
        'cajaid',
        'porce',
        'rete',
        've_obs',
        've_emp',
        've_des',
        've_coti',
        've_nc',
        've_cos',
        'v_u1',
        'v_m1',
        'v_f1',
        'v_u2',
        'v_m2',
        'v_f2',
        'v_u3',
        'v_m3',
        'v_f3',
        'v_hor',
        'v_sit',
        'v_d',
        'v_r',
        'v_p',
        'v_g',
];

}
