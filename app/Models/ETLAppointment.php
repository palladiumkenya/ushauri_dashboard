<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class ETLAppointment extends Model
{
    use HasFactory;
    use Rememberable;

    public $table = 'etl_appointment_detail';
    public $timestamps = false;
}
