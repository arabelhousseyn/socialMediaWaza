<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'message',
        'type',
        'seen',
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->locale('fr_FR')->subMinutes(2)->diffForHumans();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chats_users');
    }
}
