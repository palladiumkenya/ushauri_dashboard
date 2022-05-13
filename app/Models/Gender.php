<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    use HasFactory;

    public $table = 'tbl_gender';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];

    public function client()
    {
        return $this->belongsTo(Client::class,'gender');
    }
}