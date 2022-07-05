<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
    public $table = 'tbl_content';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
