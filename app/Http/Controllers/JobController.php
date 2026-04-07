<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * عرض قائمة بجميع الوظائف المفتوحة (للجميع)
     */
    public function index()
    {
        $jobs = Job::where('status', 'open')->latest()->paginate(10);
        
        return response()->json($jobs);
    }

    /**
     * تخزين الوظيفة الجديدة (للأدمن فقط عبر الميدل وير)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string',
            'salary'      => 'nullable|string',
            'type'        => 'required|in:full-time,part-time,remote,contract',
            'status'      => 'required|in:open,closed',
        ]);

        // ربط الوظيفة بالمستخدم (الأدمن)
        $job = Job::create(array_merge($validated, ['user_id' => Auth::id()]));

        return response()->json([
            'message' => 'تم نشر الوظيفة بنجاح!',
            'job' => $job
        ], 201);
    }

    /**
     * عرض تفاصيل وظيفة معينة (للجميع)
     */
    public function show(Job $job)
    {
        return response()->json($job);
    }

    /**
     * تحديث بيانات الوظيفة (للأدمن)
     */
    public function update(Request $request, Job $job)
    {
        // حماية إضافية للتأكد أن صاحب الوظيفة أو أدمن عام هو من يعدل
        if ($job->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'location'    => 'required|string',
            'salary'      => 'nullable|string',
            'type'        => 'required|in:full-time,part-time,remote,contract',
            'status'      => 'required|in:open,closed',
        ]);

        $job->update($validated);

        return response()->json([
            'message' => 'تم تحديث الوظيفة بنجاح!',
            'job' => $job
        ]);
    }

    /**
     * حذف الوظيفة (للأدمن)
     */
    public function destroy(Job $job)
    {
        if ($job->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'غير مصرح لك'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'تم حذف الوظيفة بنجاح']);
    }
}