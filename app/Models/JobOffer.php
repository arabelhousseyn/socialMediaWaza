<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_logo',
        'searched_profile',
        'company_name',
        'description',
        'number',
        'workplace',
        'type_of_contract',
        'status',
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
