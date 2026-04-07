<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    // أخبر لارافل بالاسم الصحيح للجدول هنا
    protected $table = 'job_posts';

    protected $fillable = [
        'title', 
        'description', 
        'location', 
        'salary', 
        'type', 
        'status', 
        'user_id'
    ];

    public function applications()
    {
        // تأكد من أن ForeignKey في جدول التقديمات هو job_id
        return $this->hasMany(Application::class, 'job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isOpen()
    {
        return $this->status === 'open';
    }
}