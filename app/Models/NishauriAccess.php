<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NishauriAccess extends Model
{
    use HasFactory;

    public $table = 'etl_nishauri_access_uptake';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [];
}
