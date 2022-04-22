<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    use HasFactory;
    public $table = 'tbl_indicators';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
