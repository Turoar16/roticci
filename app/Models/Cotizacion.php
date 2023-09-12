<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = "Cotizaciones";// <-- El nombre personalizado

    public $timestamps = false;

    protected $primaryKey = "co_id";

    public $incrementing = false;

    protected $fillable = [
        'co_pu',
        'co_dol',
        'co_rea',
        'co_pes'
];

public function getDateFormat(){
    return 'Y-d-m H:m:s';
}
}
