<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    public $table = 'tbl_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'f_name', 'm_name', 'l_name', 'dob', 'e_mail', 'email', 'phone_no', 'partner_id', 'facility_id', 'donor_id', 'f_name', 'created_at', 'updated_at', 'status', 'first_access', 'access_level', 'daily_report', 'weekly_report', 'monthly_report', 'month3_report', 'month6_report', 'Yearly_report', 'view_client', 'role_id', 'clinic_id',
    ];
    public function getAgeAttribute()
{
    // return $this->dob->diffInYears(\Carbon\Carbon::now())->format('Y-m-d');
    return Carbon::parse($this->attributes['dob'])->age;
}

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function facility(){
        return $this->belongsTo('App\Models\Facility','facility_id','code');
    }
    public function partner(){
        return $this->belongsTo('App\Partner','partner_id','id');
    }
    public function donor(){
        return $this->belongsTo('App\Donor','donor_id','id');
    }
    public function clinic(){
        return $this->belongsTo('App\Clinic','clinic_id','id');
    }
    public function role(){
        return $this->belongsTo('App\Role','role_id','id');
    }
}
