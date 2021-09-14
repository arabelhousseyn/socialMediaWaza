<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmanaImage extends Model
{
    use HasFactory;
    protected $fillable = [
        'amana_id',
        'path'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
