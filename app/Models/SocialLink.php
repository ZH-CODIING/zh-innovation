<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_info_id',
        'github',
        'linkedin',
        'facebook',
        'twitter',
        'instagram',
        'whatsapp',
        'youtube',
    ];

    public function siteInfo()
    {
        return $this->belongsTo(SiteInfo::class);
    }
}
