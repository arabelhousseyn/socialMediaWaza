<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\innovation;
class innovationDomain extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'title',
        'image',
        'type'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function innovations()
    {
        return $this->hasMany(innovation::class);
    }
}
