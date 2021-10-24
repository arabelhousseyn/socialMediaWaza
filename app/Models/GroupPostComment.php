<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class GroupPostComment extends Model
{
    use HasFactory;
    protected $fillable = [
        'group_post_id',
        'user_id',
        'comment',
        'type',
        'parent_id'
    ];

    /**
     * Get the user that owns the GroupPostComment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the replies for the GroupPostComment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(GroupPostComment::class, 'parent_id', 'id');
    }
}
