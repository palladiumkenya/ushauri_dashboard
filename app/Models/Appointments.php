<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class Appointments extends Model
{
    use HasFactory;
    use Rememberable;

    public $table = 'tbl_appointment';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'client_id', 'app_type_1', 'reason', 'appntmnt_date', 'date_attended'

    ];

    public function client(){
        return $this->belongsTo(Client::class,'client_id');
    }
}
