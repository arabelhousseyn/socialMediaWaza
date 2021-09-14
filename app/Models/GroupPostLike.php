<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPostLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_post_id',
        'user_id',
        'type'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
