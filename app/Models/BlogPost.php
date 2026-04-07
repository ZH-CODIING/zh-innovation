<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $table = 'blog_posts';

    protected $fillable = [
        'title',
        'description',   // التفاصيل
        'image',         // صورة الكافر (Cover)
        'video_url',
        'content_images', // صور لكل نص (مخزنة كـ JSON)
        'user_id',        // آيدي الكاتب
    ];

    /**
     * تحويل الحقل من JSON إلى Array تلقائياً عند التعامل معه
     */
    protected $casts = [
        'content_images' => 'array',
    ];

    /**
     * علاقة المقال بالكاتب
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * علاقة المقال بالتعليقات
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }
}