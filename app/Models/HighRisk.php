<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HighRisk extends Model
{
    use HasFactory;
    public $table = 'tbl_high_risk';
    public $timestamps = true;

    protected $primaryKey = 'id';

    protected $fillable = [
        'ccc_number',
        'mfl_code',
        'risk_score',
        'evaluation_date',
        'risk_decription',

    ];
}
