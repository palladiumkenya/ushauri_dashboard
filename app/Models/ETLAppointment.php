<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ETLAppointment extends Model
{
    use HasFactory;
    public $table = 'etl_appointment_detail';
    public $timestamps = false;
}
