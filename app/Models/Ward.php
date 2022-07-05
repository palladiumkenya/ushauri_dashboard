<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;
    public $table = 'tbl_ward';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
