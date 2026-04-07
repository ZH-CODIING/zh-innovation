<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // حالة نجاح: تم استرجاع جميع جهات الاتصال بنجاح.
        // يتم إرجاع جميع سجلات الـ Contact كـ JSON مع رمز حالة HTTP 200 (OK) افتراضيًا.
        return response()->json(Contact::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // محاولة التحقق من صحة البيانات المرسلة.
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:255',
                'service' => 'nullable|string|max:255',
                'message' => 'nullable|string',
            ]);

            // حالة نجاح: إذا كانت البيانات صحيحة، يتم إنشاء سجل جديد لجهة الاتصال.
            // يتم إرجاع كائن جهة الاتصال التي تم إنشاؤها كـ JSON مع رمز حالة HTTP 201 (Created).
            $contact = Contact::create($validated);
            return response()->json($contact, 201);

        } catch (ValidationException $e) {
            // حالة فشل: فشل التحقق من صحة البيانات.
            // يتم إرجاع رسائل الأخطاء كـ JSON مع رمز حالة HTTP 422 (Unprocessable Entity).
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        // حالة نجاح: تم استرجاع جهة الاتصال المطلوبة بنجاح.
        // Laravel يقوم تلقائيًا بجلب الـ Contact بناءً على الـ ID في الـ URL (model implicit binding).
        // يتم إرجاع كائن جهة الاتصال كـ JSON مع رمز حالة HTTP 200 (OK) افتراضيًا.
        return response()->json($contact);

        // ملاحظة: إذا لم يتم العثور على المورد (Contact),
        // فإن Laravel سيطلق تلقائيًا استثناء ModelNotFoundException،
        // والذي عادة ما يؤدي إلى إرجاع رمز حالة HTTP 404 (Not Found) بشكل افتراضي.
        // لا نحتاج للتعامل مع هذا يدويًا هنا.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        try {
            // محاولة التحقق من صحة البيانات المرسلة للتحديث.
            // 'sometimes' تعني أن الحقل سيتم التحقق من صحته فقط إذا كان موجودًا في الطلب.
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255',
                'phone' => 'nullable|string|max:255',
                'service' => 'nullable|string|max:255',
                'message' => 'nullable|string',
            ]);

            // حالة نجاح: إذا كانت البيانات صحيحة، يتم تحديث سجل جهة الاتصال.
            // يتم إرجاع كائن جهة الاتصال المحدث كـ JSON مع رمز حالة HTTP 200 (OK) افتراضيًا.
            $contact->update($validated);
            return response()->json($contact);

        } catch (ValidationException $e) {
            // حالة فشل: فشل التحقق من صحة البيانات.
            // يتم إرجاع رسائل الأخطاء كـ JSON مع رمز حالة HTTP 422 (Unprocessable Entity).
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        // حالة نجاح: تم حذف جهة الاتصال بنجاح.
        // يتم إرجاع استجابة فارغة (null) مع رمز حالة HTTP 204 (No Content)، مما يشير إلى نجاح العملية بدون محتوى لإرجاعه.
        $contact->delete();
        return response()->json(null, 204);

        // ملاحظة: إذا لم يتم العثور على المورد (Contact) للحذف،
        // فإن Laravel سيطلق تلقائيًا استثناء ModelNotFoundException،
        // والذي عادة ما يؤدي إلى إرجاع رمز حالة HTTP 404 (Not Found) بشكل افتراضي.
        // لا نحتاج للتعامل مع هذا يدويًا هنا.
    }
}