<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'path',
        'name_company',
        'sector',
        'address',
        'job',
        'status',
        'state',
        'price',
        'description',
        'mission',
        'profile',
        'advantage'
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
