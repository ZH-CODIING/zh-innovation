<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'github_link',
        'live_link',
        'project_type',
        'duration',
        'end_date',
        'location',
    ];

    protected $casts = [
        'end_date' => 'date',
    ];
}
