<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NishauriUser extends Model
{
    use HasFactory;

    public $table = 'tbl_nishauri_users';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
