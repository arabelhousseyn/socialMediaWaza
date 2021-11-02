<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'image',
        'description',
        'source',
        'colorabble',
        'type',
        'is_approved',
        'anonym',
        'title_pitch',
        'video'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function images()
    {
        return $this->hasMany(GroupPostImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likesList()
    {
        return $this->hasMany(GroupPostLike::class);
    }

    public function comments()
    {
        return $this->hasMany(GroupPostComment::class);
    }


    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
