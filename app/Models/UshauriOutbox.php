<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UshauriOutbox extends Model
{
    use HasFactory;
    
    public $table = 'tbl_clnt_outgoing';
    public $timestamps = false;

    protected $fillable = [
        'destination', 'source', 'msg', 'updated_at', 'deleted_at', 'created_at', 'status', 'responded', 'message_type_id', 'content_id', 'recepient_type', 'created_by', 'updated_by', 'is_deleted', 'clnt_usr_id', 'ushauri_id', 'db_source', 'message_id', 'cost', 'callback_status', 'failure_reason'
    ];
}
