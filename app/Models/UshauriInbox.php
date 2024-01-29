<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UshauriInbox extends Model
{
    use HasFactory;
    // protected $connection = 'ushauri';
    public $table = 'tbl_incoming';
    public $timestamps = false;

    protected $fillable = [
        'destination', 'source', 'msg', 'receivedtime', 'reference', 'LinkId'
    ];
}
