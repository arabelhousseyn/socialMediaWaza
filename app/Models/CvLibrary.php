<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvLibrary extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'path',
        'FullName',
        'dob',
        'arabic',
        'english',
        'french',
        'phone',
        'email',
        'area',
        'description'
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }
}
