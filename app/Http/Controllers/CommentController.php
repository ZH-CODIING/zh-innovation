<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * إضافة تعليق جديد أو رد (Store)
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'comment_text' => 'required|string|max:1000',
            'parent_id'    => 'nullable|exists:comments,id', // إذا وُجد، فهذا رد
        ]);

        // التأكد من وجود المقال
        $post = BlogPost::findOrFail($postId);

        $comment = Comment::create([
            'comment_text' => $request->comment_text,
            'user_id'      => Auth::id(),
            'blog_post_id' => $post->id,
            'parent_id'    => $request->parent_id, // سيكون null في التعليق العادي، وله قيمة في الرد
        ]);

        return response()->json([
            'message' => 'تم إضافة التعليق بنجاح',
            'comment' => $comment->load('user')
        ], 201);
    }

    /**
     * تحديث تعليق (Update)
     */
    public function update(Request $request, Comment $comment)
    {
        // التأكد أن صاحب التعليق هو من يعدله
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'غير مصرح لك بتعديل هذا التعليق'], 403);
        }

        $request->validate([
            'comment_text' => 'required|string|max:1000',
        ]);

        $comment->update([
            'comment_text' => $request->comment_text
        ]);

        return response()->json(['message' => 'تم تحديث التعليق', 'comment' => $comment]);
    }

    /**
     * حذف تعليق (Destroy)
     */
    public function destroy(Comment $comment)
    {
        // يسمح بالحذف لصاحب التعليق أو الأدمن
        if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'لا تملك صلاحية الحذف'], 403);
        }

        $comment->delete(); // سيقوم بحذف الردود أيضاً بسبب onDelete('cascade') في الميجريشن

        return response()->json(['message' => 'تم حذف التعليق بنجاح'], 200);
    }
}