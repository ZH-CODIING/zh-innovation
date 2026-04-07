<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class ProjectDemo extends Model
{
protected $table = 'projects_demo';


protected $fillable = [
'name',
'description',
'technologies',
'images',
'cover_image',
'demo_link',
'type',
];


protected $casts = [
'technologies' => 'array',
'images' => 'array',
];
}