<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = "ar_id";

    public $incrementing = false;

    protected $fillable = [
        'ar_cod',
        'ar_codbar',
        'ar_codi',
        'ar_des',
        'ar_ref',
        'ar_tipart',
        'ar_ume',
        'ar_pre',
        'ar_premin',
        'ar_preven',
        'ar_p30',
        'ar_p60',
        'ar_p90',
        'ar_p120',
        'ar_pcomi',
        'ar_pmayo',
        'ar_ctable',
        'ar_imag',
        'ar_genstk',
        'ar_tc',
        'ar_mi',
        'ar_po',
        'ar_ma',
        'ar_tg',
        'ar_ven',
        'ar_cc',
        'ar_pc',
        'ar_cpc',
        'ar_max',
        'ar_u1',
        'ar_m1',
        'ar_f1',
        'ar_u2',
        'ar_m2',
        'ar_f2',
        'ar_ubi',
];

public function getDateFormat(){
    return 'Y-d-m H:m:s';
}

    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

    public function getImagenAttribute()
    {
        if (file_exists('storage/products/' . $this->image))
            return $this->image;
        else
            return 'no-image.png';
    }
}
