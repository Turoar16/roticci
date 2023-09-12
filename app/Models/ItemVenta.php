<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVenta extends Model
{
    use HasFactory;

    protected $table = "itemventas";// <-- El nombre personalizado

    public $timestamps = false;

    protected $primaryKey = "id_itven";

    public $incrementing = false;

    protected $fillable = [
        'it_pu',
        'acti',
        'cant',
        'puni',
        'excen',
        'iva5',
        'iva10',
        'idven',
        'dep',
        'tipo',
        'it_sit',
        'it_pcos',
        'it_por',
        'it_des',
        'it_subtot',
        'it_cca',
        'it_pca',
        'it_pcosc',
        'it_v1',
        'it_v2',
        'it_ids',
];
}
