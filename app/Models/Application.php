<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = [
        'job_id',
        'full_name',
        'phone',
        'whatsapp',
        'resume',
        'cover_letter',
        'status'
    ];

    // علاقة الطلب بالوظيفة
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}