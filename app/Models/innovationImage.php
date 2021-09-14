<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class innovationImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'path',
        'innovation_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
