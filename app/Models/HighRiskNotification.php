<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HighRiskNotification extends Model
{
    use HasFactory;
    public $table = "vw_highrisk_notification";
}
