<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeReport extends Model
{
    use HasFactory;

    public $table = 'partner_outcome_report';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [];
}
