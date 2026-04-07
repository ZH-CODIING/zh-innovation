<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_text',
        'user_id',
        'blog_post_id',
        'parent_id', // هذا الحقل هو المسؤول عن تحديد ما إذا كان التعليق رداً أم لا
    ];

    /**
     * العلاقة مع المستخدم (صاحب التعليق)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع المقال (BlogPost)
     */
    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class);
    }

    /**
     * العلاقة مع التعليق الأب (في حالة كان هذا التعليق عبارة عن "رد")
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * العلاقة مع الردود (التعليقات التابعة لهذا التعليق)
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('user'); 
        // أضفنا with('user') لجلب بيانات صاحب الرد تلقائياً
    }
}