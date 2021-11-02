<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freelance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'searched_profile',
        'description',
        'date',
        'duration',
        'region',
        'status'
    ];

    protected $hidden = [
        'created_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
