<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Txcurr extends Model
{
    use HasFactory;
    public $table = 'tbl_tx_cur';
    public $timestamps = false;
}
