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

    protected $fillable = [
        'destination','msg','responded','status','message_type_id','source','clnt_usr_id','appointment_id','no_of_days','recepient_type','content_id','created_by'
    ];
}
