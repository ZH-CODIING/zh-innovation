<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // أضفنا هذا لاستخراج id المستخدم الحالي

class BlogPostController extends Controller
{
     public function index()
    {
        // استخدام get() بدلاً من all() لجلب النتائج بعد الترتيب والتحميل المسبق
        $posts = BlogPost::with('user')
            ->latest()
            ->get();

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'          => 'required|string|max:255',
                'description'    => 'required|string',
                'image'          => 'nullable|image|max:2048', // صورة الغلاف
                'video_url'      => 'nullable|url',
                'content_images' => 'nullable|array', // توقع مصفوفة صور
                'content_images.*' => 'image|max:2048', // كل ملف داخل المصفوفة يجب أن يكون صورة
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $dataToCreate = $request->except(['_token', 'content_images']);

        // 1. ربط المقال بالكاتب (المستخدم المسجل حالياً)
        $dataToCreate['user_id'] = Auth::id();

        // 2. معالجة صورة الغلاف (Cover Image)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('blog_posts/covers', 'public');
            $dataToCreate['image'] = Storage::url($path);
        }

        // 3. معالجة صور المحتوى المتعددة (Content Images)
        if ($request->hasFile('content_images')) {
            $uploadedImages = [];
            foreach ($request->file('content_images') as $file) {
                $path = $file->store('blog_posts/content', 'public');
                $uploadedImages[] = Storage::url($path);
            }
            $dataToCreate['content_images'] = $uploadedImages; // ستخزن كـ JSON بفضل cast في الموديل
        }

        $blogPost = BlogPost::create($dataToCreate);
        return response()->json($blogPost, 201);
    }

    public function show(BlogPost $blogPost)
    {
        // عرض المقال مع الكاتب والتعليقات والردود
        return response()->json($blogPost->load(['user', 'comments.replies']));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        // التأكد أن الكاتب هو نفسه من يحاول التعديل (أو أدمن)
        if (Auth::id() !== $blogPost->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'غير مسموح لك بتعديل هذا المقال'], 403);
        }

        try {
            $validated = $request->validate([
                'title'            => 'nullable|string|max:255',
                'description'      => 'nullable|string',
                'image'            => 'nullable|image|max:2048',
                'video_url'        => 'nullable|url',
                'content_images'   => 'nullable|array',
                'content_images.*' => 'image|max:2048',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $dataToUpdate = $request->except(['_token', '_method', 'content_images']);

        // تحديث صورة الغلاف وحذف القديمة
        if ($request->hasFile('image')) {
            if ($blogPost->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $blogPost->image));
            }
            $path = $request->file('image')->store('blog_posts/covers', 'public');
            $dataToUpdate['image'] = Storage::url($path);
        }

        // تحديث صور المحتوى (إضافة صور جديدة أو استبدال الكل)
        if ($request->hasFile('content_images')) {
            // حذف الصور القديمة من السيرفر أولاً
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
        return response()->json($blogPost);
    }

    public function destroy(BlogPost $blogPost)
    {
        // حذف صورة الغلاف
        if ($blogPost->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $blogPost->image));
        }

        // حذف جميع صور المحتوى
        if ($blogPost->content_images) {
            foreach ($blogPost->content_images as $path) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $path));
            }
        }

        $blogPost->delete();
        return response()->json(null, 204);
    }
}