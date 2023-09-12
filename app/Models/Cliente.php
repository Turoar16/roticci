<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $primaryKey = "cli_cod";

    public $incrementing = false;

    protected $fillable = [
        'cli_nom',
        'cli_ape',
        'cli_dir',
        'cli_tel',
        'cli_cel',
        'cli_cre',
        'cli_ruc',
        'cli_obs',
        // 'cli_lim',
        // 'cli_com',
    ];

    public function getDateFormat(){
        return 'Y-d-m H:m:s';
    }

}
