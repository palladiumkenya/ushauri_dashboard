<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveFacilities extends Model
{
    use HasFactory;
    public $table = 'active_facilities';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
