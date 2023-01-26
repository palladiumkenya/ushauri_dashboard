<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenderAppointment extends Model
{
    use HasFactory;
    public $table = 'etl_appointment_gender';
    public $timestamps = false;
}
