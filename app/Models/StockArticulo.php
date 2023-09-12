<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockArticulo extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $primaryKey = "sto_id";

    public $incrementing = false;

    protected $fillable = [
        'sto_arti',
        'sto_codi',
        'sto_cant',
        'sto_depo',
        'sto_venci',
        'sto_ven',
        'sto_uni',
        'sto_pedi',
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
