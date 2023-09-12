<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransferencia extends Model
{
    use HasFactory;

    protected $table = "Item_Transferencia";// <-- El nombre personalizado

    public $timestamps = false;

    protected $primaryKey = "id_item";

    public $incrementing = false;

    protected $fillable = [
        'it_pu',
        'num',
        'art',
        'cant',
    ];

    public function getDateFormat(){
        return 'Y-d-m H:m:s';
    }
}
