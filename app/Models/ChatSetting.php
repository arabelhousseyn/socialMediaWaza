<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'block_notification',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
