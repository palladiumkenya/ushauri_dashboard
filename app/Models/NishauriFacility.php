<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NishauriFacility extends Model
{
    use HasFactory;
    public $table = 'etl_nishauri_facility';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
