<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * حفظ طلب التقديم الجديد (Store)
     */
    public function store(Request $request, $jobId)
    {
        // 1. التحقق من البيانات المدخلة
        $request->validate([
            'full_name'    => 'required|string|max:255',
            'phone'        => 'required|string|max:20',
            'whatsapp'     => 'nullable|string|max:20',
            'resume'       => 'required|mimes:pdf,doc,docx|max:2048', // ملف PDF أو Word، بحد أقصى 2MB
            'cover_letter' => 'nullable|string|min:10',
        ]);

        // 2. التأكد من أن الوظيفة موجودة ومفتوحة للتقديم
        $job = Job::findOrFail($jobId);
        if ($job->status !== 'open') {
            return back()->with('error', 'عذراً، هذه الوظيفة لم تعد تقبل طلبات جديدة.');
        }

        // 3. معالجة رفع ملف السيرة الذاتية
        $resumePath = null;
        if ($request->hasFile('resume')) {
            // سيتم تخزين الملف في storage/app/public/resumes
            $resumePath = $request->file('resume')->store('resumes', 'public');
        }

        // 4. إنشاء الطلب في قاعدة البيانات
        Application::create([
            'job_id'       => $job->id,
            'full_name'    => $request->full_name,
            'phone'        => $request->phone,
            'whatsapp'     => $request->whatsapp,
            'resume'       => $resumePath,
            'cover_letter' => $request->cover_letter,
            'status'       => 'pending', // الحالة الافتراضية
        ]);

        return back()->with('success', 'تم إرسال طلبك بنجاح! سنتواصل معك قريباً.');
    }

    /**
     * عرض جميع الطلبات المقدمة لوظيفة معينة (لوحة تحكم صاحب العمل)
     */
public function index($jobId)
{
    $job = Job::with('applications')->findOrFail($jobId);
    
    // حماية: تأكد أن الأدمن فقط أو صاحب الوظيفة هو من يرى الطلبات
    if (auth()->user()->role !== 'admin' && auth()->id() !== $job->user_id) {
        return response()->json(['message' => 'غير مصرح لك'], 403);
    }

    return response()->json([
        'job' => $job->title,
        'applications' => $job->applications
    ]);
}

    /**
     * تحديث حالة الطلب (قبول / رفض)
     */
    public function updateStatus(Request $request, Application $application)
    {
        $request->validate([
            'status' => 'required|in:pending,accepted,rejected'
        ]);

        $application->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }
}