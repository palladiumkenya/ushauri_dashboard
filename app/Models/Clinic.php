<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Clinic extends Model
{
    use HasFactory;
    use Rememberable;

    public $table = 'tbl_clinic';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
