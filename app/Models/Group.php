<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'logo',
        'type',
        'gender',
        'minAge',
        'maxAge',
        'group_universe_id',
        'large_cover',
        'description'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function universe()
    {
        return $this->belongsTo(GroupUniverse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(GroupPost::class);
    }

    public function linkInformation()
    {
        return $this->hasMany(groupLink::class);
    }
}
