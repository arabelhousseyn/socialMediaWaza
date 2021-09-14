<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class innovationComment extends Model
{
    use HasFactory;
    protected $fillable = [
        'innovation_id',
        'user_id',
        'comment',
        'type'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
