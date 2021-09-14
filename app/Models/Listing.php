<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'status',
        'lat',
        'long',
        'area'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function images()
    {
        return $this->hasMany(ListingImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
