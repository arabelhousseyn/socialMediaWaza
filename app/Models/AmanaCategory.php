<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmanaCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'path',
        'type'
    ];

    protected $hidden = [
        'updated_at'
    ];
}
