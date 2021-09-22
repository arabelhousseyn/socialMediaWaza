<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GroupUniverse;
use Auth;
class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
        'cover',
        'type',
        'gender',
        'minAge',
        'maxAge',
        'group_universe_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    public function universe()
    {
        return $this->belongsTo(GroupUniverse::class);
    }
}
