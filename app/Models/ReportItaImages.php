<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportItaImages extends Model
{
    use HasFactory;
    protected $fillable = [
        'report_ita_id',
        'path'
    ];

    protected $hidden = [
        'updated_at'
    ];
}
