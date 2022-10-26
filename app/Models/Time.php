<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    use HasFactory;
    public $table = 'tbl_time';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
