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
        'imageCompany',
        'status'
    ];

    protected $hidden = [
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

    public function scopeSelective($query,$innovation_domain_id)
    {

        return ($innovation_domain_id == 0) ? $query->select('id','title','user_id','type','imageCompany')->where('status',1)->orderBy('id','DESC')
        : $query->select('id','title','user_id','type','imageCompany','created_at')
        ->where([['innovation_domain_id','=',$innovation_domain_id],['status','=',0]])->orderBy('id','DESC');
    }
}
