<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{
    GroupUniverse,
    GroupPost
};
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
        'group_universe_id',
        'large_cover',
        'description'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    public function universe()
    {
        return $this->belongsTo(GroupUniverse::class);
    }

    /**
     * Get the user that owns the Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the comments for the Group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(GroupPost::class);
    }
}
