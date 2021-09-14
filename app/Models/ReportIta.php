<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportIta extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'lat',
        'long',
        'adress',
        'markVehicle',
        'description'
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(ReportItaImages::class);
    }
}
