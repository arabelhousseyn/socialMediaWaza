<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amana extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'user_id',
        'abbreviation',
        'amana_category_id',
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function images()
    {
        return $this->hasMany(AmanaImage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
