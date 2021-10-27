<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class groupLink extends Model
{
    use HasFactory;
    protected $fillable = [
        'link',
        'name_link',
        'icon_link',
        'group_id'
    ];
}

