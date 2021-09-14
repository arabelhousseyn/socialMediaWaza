<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reportable_id',
        'reportable_type'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function reportable()
    {
        return $this->morphTo();
    }
}
