<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\innovationDomain;
use App\Models\innovationImage;
use App\Models\innovationLike;
use App\Models\User;
class innovation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'audio',
        'is_financed',
        'financementAmount',
        'pathBusinessPlan',
        'user_id',
        'innovation_domain_id',
        'likes',
        'type',
        'imageCompany'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function domain()
    {
        return $this->belongsTo(innovationDomain::class);
    }

    public function images()
    {
        return $this->hasMany(innovationImage::class);
    }

    public function likesList()
    {
        return $this->hasMany(innovationLike::class);
    }

    public function comments()
    {
        return $this->hasMany(innovationComment::class);
    }
}
