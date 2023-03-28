<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HighRisk extends Model
{
    use HasFactory;
    public $table = 'tbl_high_risk';
    public $timestamps = true;

}
