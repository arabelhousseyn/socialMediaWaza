<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;
use App\Models\User;
class Wilaya extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'country_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeBycountry($query,$id)
    {
        return $query->where('country_id', $id);
    }
}
