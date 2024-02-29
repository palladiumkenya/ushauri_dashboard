<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectoryLog extends Model
{
    use HasFactory;

    public $table = 'tbl_directory_search_logs';
    public $timestamps = true;
    public $incrementing = false;

    protected $fillable = ['search_term', 'result_count'];
}
