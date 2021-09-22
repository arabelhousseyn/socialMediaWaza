<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Wilaya;
use App\Models\FaceVerification;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
        'is_kaiztech_team'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'remember_token',
        'password',
        'code_verification'
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
