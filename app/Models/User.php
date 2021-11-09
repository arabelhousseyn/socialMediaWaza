<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'fullName',
        'subName',
        'dob',
        'picture',
        'gender',
        'profession',
        'wilaya_id',
        'phone',
        'email',
        'password',
        'email_verified_at',
        'is_freelancer',
        'receive_ads',
        'token',
        'is_verified',
        'code_verification',
        'hide_phone',
        'is_kaiztech_team',
        'company',
        'website',
        'device_token'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'remember_token',
        'code_verification',
        'password'
    ];

    public function verification()
    {
        return $this->hasOne(FaceVerification::class);
    }

    public function wilaya()
    {
        return $this->belongsTo(Wilaya::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, follower::class, 'follow_id', 'user_id')->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, follower::class, 'user_id', 'follow_id')->withTimestamps();
    }


    public function followingGroup()
    {
        return $this->belongsToMany(User::class, followGroup::class, 'user_id', 'follow_id')->withTimestamps();
    }


    public function posts()
    {
        return $this->hasMany(GroupPost::class);
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chats_users');
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
