<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientOutgoing extends Model
{
    use HasFactory;

    public $table = 'tbl_clnt_outgoing';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [];
}
