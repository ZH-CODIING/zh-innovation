<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteInfo extends Model
{
    use HasFactory;

    protected $table = 'site_info';

    protected $fillable = [
        'logo',
        'cv',
        'name',
        'description',
        'job_title',
        'experience_years',
        'hero_section_image',
        'email',
        'phone',
        'address',
        'footer_description',
        'copyright_text',
    ];

    public function socialLinks()
    {
        return $this->hasOne(SocialLink::class);
    }
}
