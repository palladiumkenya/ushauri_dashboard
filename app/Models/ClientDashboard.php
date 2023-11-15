<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Watson\Rememberable\Rememberable;

class ClientDashboard extends Model
{
    use HasFactory;
    use Rememberable;

    public $table = 'etl_client_dashboard';
    public $timestamps = false;
}
