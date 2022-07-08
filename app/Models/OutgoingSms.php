<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutgoingSms extends Model
{
    use HasFactory;

    public $table = "vw_outgoing_sms";
}
