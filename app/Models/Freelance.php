<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freelance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'job_offer_id',
        'description',
        'date',
        'duration',
        'area'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

 

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
