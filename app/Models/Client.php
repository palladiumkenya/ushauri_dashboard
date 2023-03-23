<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Watson\Rememberable\Rememberable;

class Client extends Model
{
    use HasFactory;
    use Rememberable;

    public $table = 'tbl_client';
    public $timestamps = false;

    protected $fillable = [

        'clinic_number', 'consent_date', 'smsenable', 'language_id', 'motivational_enable', 'txt_time', 'phone_no'
    ];
//     protected $appends = ['age'];

// public function getAgeAttribute()
// {
//     return Carbon::parse($this->attributes['dob'])->age;
// }
    // public function appointment()
    // {
    //     return $this->hasMany(Appointments::class, 'client_id');
    // }

    // Public function partner()
    // {
    //     return $this->hasOne(Partner::class);
    // }


}
