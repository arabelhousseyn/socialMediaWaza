<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;
    protected $fillable = [
        'cv_library_id',
        'text',
        'grade',
        'from',
        'to'
    ];

    protected $hidden = [
        'updated_at'
    ];
}
