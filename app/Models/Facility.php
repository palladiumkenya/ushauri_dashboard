<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Facility extends Model
{
    use HasFactory;
    use Rememberable;

    public $table = 'tbl_master_facility';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'mfl_code');
    }
}
