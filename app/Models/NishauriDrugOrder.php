<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NishauriDrugOrder extends Model
{
    use HasFactory;

    public $table = 'tbl_nishauri_drug_delivery';
    public $timestamps = true;
    public $incrementing = false;

    protected $fillable = ['order_id', 'initiated_by'];
}
