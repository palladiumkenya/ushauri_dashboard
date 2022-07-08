<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissedAppointmentNotifications extends Model
{
    use HasFactory;

    public $table = "vw_missed_appointment_notifications";
}
