<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaritalAppointment extends Model
{
    use HasFactory;
    public $table = 'etl_appointment_marital';
    public $timestamps = false;
}
