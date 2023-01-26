<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeAppointment extends Model
{
    use HasFactory;
    public $table = 'etl_appointment_age';
    public $timestamps = false;
}
