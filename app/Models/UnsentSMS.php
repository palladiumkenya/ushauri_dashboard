<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnsentSMS extends Model
{
    use HasFactory;

    public $table = "vw_unsent_sms";
}
