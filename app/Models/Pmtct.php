<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pmtct extends Model
{
    use HasFactory;
    public $table = 'tbl_pmtct';
    public $timestamps = false;
    public $incrementing = false;
    
    protected $fillable = [
        
    ];
}
