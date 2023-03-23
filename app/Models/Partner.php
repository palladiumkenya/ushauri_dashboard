<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Partner extends Model
{
    use HasFactory;
    use Rememberable;

    public $table = 'tbl_partner';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
