<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UshauriProgram extends Model
{
    use HasFactory;
    public $table = 'etl_ushauri_program_indicators';
    public $timestamps = false;

    protected $fillable = [];
}
