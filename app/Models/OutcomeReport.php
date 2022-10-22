<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeReport extends Model
{
    use HasFactory;

    public $table = 'tbl_outcome_report_raw';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [

    ];
}
