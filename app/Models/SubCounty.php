<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class SubCounty extends Model
{
    use HasFactory;
    use Rememberable;

    public $table = 'tbl_sub_county';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
