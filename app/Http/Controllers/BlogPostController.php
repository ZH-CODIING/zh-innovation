<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BlogPostController extends Controller
{
    /**
     * جلب جميع المقالات مع بيانات الكاتب مرتبة من الأحدث للأقدم
     */
    public function index()
    {
        try {
            $posts = BlogPost::with('user')
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $posts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المقالات'
            ], 500);
        }
    }

    /**
     * إنشاء مقال جديد مع دعم رفع الصور المتعددة
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // صورة الغلاف
            'video_url'      => 'nullable|url',
            'content_images' => 'nullable|array',
            'content_images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        $dataToCreate = $request->except(['content_images']);
        $dataToCreate['user_id'] = Auth::id();

        // معالجة صورة الغلاف الأساسية
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blog_posts/covers', 'public');
            $dataToCreate['image'] = Storage::url($path);
        }

        // معالجة مصفوفة الصور داخل المحتوى
        if ($request->hasFile('content_images')) {
            $uploadedImages = [];
            foreach ($request->file('content_images') as $file) {
                $path = $file->store('blog_posts/content', 'public');
                $uploadedImages[] = Storage::url($path);
            }
            $dataToCreate['content_images'] = $uploadedImages; 
        }

        $blogPost = BlogPost::create($dataToCreate);

        return response()->json([
            'success' => true,
            'message' => 'تم نشر المقال بنجاح',
            'data' => $blogPost
        ], 201);
    }

    /**
     * عرض تفاصيل مقال معين مع التعليقات والردود
     */
    public function show(BlogPost $blogPost)
    {
        return response()->json([
            'success' => true,
            'data' => $blogPost->load(['user', 'comments.replies'])
        ]);
    }

    /**
     * تحديث المقال مع تنظيف الصور القديمة من السيرفر
     */
    public function update(Request $request, BlogPost $blogPost)
    {
        // التحقق من الصلاحيات: صاحب المقال أو المدير فقط
        if (Auth::id() !== $blogPost->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'غير مسموح لك بتعديل هذا المقال'], 403);
        }

        $request->validate([
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'video_url'   => 'nullable|url',
        ]);

        $dataToUpdate = $request->except(['_method', 'content_images']);

        // تحديث صورة الغلاف وحذف القديمة لتقليل استهلاك المساحة
        if ($request->hasFile('image')) {
            if ($blogPost->image) {
                $oldCover = str_replace('/storage/', '', $blogPost->image);
                Storage::disk('public')->delete($oldCover);
            }
            $path = $request->file('image')->store('blog_posts/covers', 'public');
            $dataToUpdate['image'] = Storage::url($path);
        }

        // تحديث صور المحتوى
        if ($request->hasFile('content_images')) {
            // حذف الصور القديمة
            if ($blogPost->content_images) {
                foreach ($blogPost->content_images as $oldPath) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $oldPath));
                }
            }

            $uploadedImages = [];
            foreach ($request->file('content_images') as $file) {
                $path = $file->store('blog_posts/content', 'public');
                $uploadedImages[] = Storage::url($path);
            }
            $dataToUpdate['content_images'] = $uploadedImages;
        }

        $blogPost->update($dataToUpdate);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المقال بنجاح',
            'data' => $blogPost
        ]);
    }

    /**
     * حذف المقال نهائياً مع كافة ملفاته
     */
    public function destroy(BlogPost $blogPost)
    {
        // 1. حذف صورة الغلاف
        if ($blogPost->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $blogPost->image));
        }

        // 2. حذف صور المحتوى المتعددة
        if ($blogPost->content_images) {
            foreach ($blogPost->content_images as $path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $path));
            }
        }

        $blogPost->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المقال وكافة ملحقاته بنجاح'
        ], 200);
    }
}
