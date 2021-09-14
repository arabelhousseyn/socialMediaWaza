<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPostImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'group_post_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
