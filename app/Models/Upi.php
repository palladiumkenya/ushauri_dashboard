<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upi extends Model
{
    use HasFactory;
    public $table = 'tbl_moh_upi_logs';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
