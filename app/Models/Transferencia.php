<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    use HasFactory;

    protected $table = "Transferencia";// <-- El nombre personalizado

    public $timestamps = false;

    protected $primaryKey = "id_tra";

    public $incrementing = false;

    protected $fillable = [
        't_pu',
        'ori',
        'des',
        'fec',
        'est',
        'tran_obs',
    ];

    public function getDateFormat(){
        return 'Y-d-m H:m:s';
    }
}
