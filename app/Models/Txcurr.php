<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Txcurr extends Model
{
    use HasFactory;
    use Rememberable;

    public $table = 'tbl_tx_cur';
    public $timestamps = false;
}
