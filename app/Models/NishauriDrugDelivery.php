<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NishauriDrugDelivery extends Model
{
    use HasFactory;

    public $table = 'tbl_nishauri_drug_order';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [];

}
