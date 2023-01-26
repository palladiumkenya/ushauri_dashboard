<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ETLClient extends Model
{
    use HasFactory;
    public $table = 'etl_client_detail';
    public $timestamps = false;
}
