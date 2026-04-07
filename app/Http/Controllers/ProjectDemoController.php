<?php

namespace App\Http\Controllers;

use App\Models\ProjectDemo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log; // لاستخدام واجهة تسجيل الأخطاء (Log Facade)
use Throwable; // لالتقاط أي نوع من الأخطاء

class ProjectDemoController extends Controller
{
    // ----------------------------------------------------------------------
    // 1. القراءة (Read Operations)
    // ----------------------------------------------------------------------

    /**
     * قائمة المشاريع (Projects Index).
     */
    public function index(): JsonResponse
    {
        $projects = ProjectDemo::latest()->get();
        
        // تطبيق دالة تحويل الروابط على كل مشروع
        $projects->transform(fn ($p) => $this->formatProjectResponse($p));

        return response()->json(['status' => 200, 'data' => $projects]);
    }

    /**
     * عرض مشروع واحد (Show Single Project).
     */
    public function show(int $id): JsonResponse
    {
        try {
            $project = ProjectDemo::findOrFail($id);
            return response()->json([
                'status' => 200, 
                'data' => $this->formatProjectResponse($project)
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             return response()->json([
                'status' => 404, 
                'message' => 'المشروع غير موجود.'
            ], 404);
        } catch (Throwable $e) {
            Log::error('فشل في عرض المشروع:', ['project_id' => $id, 'error_message' => $e->getMessage()]);
            return response()->json(['status' => 500, 'message' => 'فشل داخلي في الخادم (500). يرجى مراجعة سجلات الأخطاء في المشروع.'], 500);
        }
    }


    // ----------------------------------------------------------------------
    // 2. الإنشاء (Create Operation) - مع إضافة تسجيل الأخطاء
    // ----------------------------------------------------------------------

    /**
     * إنشاء مشروع جديد (Store Project).
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // التحقق من صحة البيانات - هذه هي الدالة التي كانت مفقودة
            $validated = $this->validateProjectData($request);

            // التعامل مع الصور ورفعها
            $coverPath = $this->uploadSingleImage($request, 'cover_image', 'projects_demo/covers');
            $imagesPaths = $this->uploadMultipleImages($request, 'images', 'projects_demo/images');

            // إنشاء المشروع في قاعدة البيانات
            $project = ProjectDemo::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'technologies' => $validated['technologies'] ?? null,
                'images' => $imagesPaths ?: null,
                'cover_image' => $coverPath,
                'demo_link' => $validated['demo_link'] ?? null,
                'type' => $validated['type'] ?? null,
            ]);

            return response()->json([
                'status' => 201, 
                'message' => 'تم إنشاء المشروع بنجاح.', 
                'data' => $this->formatProjectResponse($project)
            ], 201);
            
        } catch (Throwable $e) {
            // تسجيل الخطأ: يتم تسجيل تفاصيل الخطأ (الرسالة، مسار الملف، رقم السطر)
            Log::error('فشل في إنشاء المشروع الجديد:', [
                'error_message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'request_data' => $request->all() // سجل بيانات الطلب للمراجعة
            ]);
            
            // إرجاع استجابة خطأ موحدة للمستخدم
            return response()->json([
                'status' => 500, 
                'message' => 'فشل داخلي في الخادم (500). يرجى مراجعة سجلات الأخطاء في المشروع.'
            ], 500);
        }
    }

    // ----------------------------------------------------------------------
    // 3. التحديث (Update Operation) - مع إضافة تسجيل الأخطاء
    // ----------------------------------------------------------------------

    /**
     * تعديل مشروع موجود (Update Project).
     */
  public function update(Request $request, int $id): JsonResponse
{
    try {
        $project = ProjectDemo::findOrFail($id);

        $validated = $this->validateProjectData($request, true);

        // 1️⃣ تحديث صورة الغلاف
        $this->handleCoverImageUpdate($request, $project);

        // 2️⃣ تحديث صور المعرض
        $this->handleGalleryImagesUpdate($request, $project);

        // 3️⃣ حذف أي حقول غير موجودة في الجدول
        $dataToUpdate = collect($validated)
            ->except(['images', 'cover_image', 'remove_images'])
            ->toArray();

        // 4️⃣ تحديث البيانات النصية فقط
        $project->update($dataToUpdate);

        // 5️⃣ حفظ التعديلات الخاصة بالصور
        $project->save();

        return response()->json([
            'status' => 200,
            'message' => 'تم تحديث المشروع بنجاح.',
            'data' => $this->formatProjectResponse($project)
        ]);

    } catch (Throwable $e) {
        Log::error('فشل في تحديث المشروع:', [
            'project_id' => $id,
            'error_message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
            'request_data' => $request->all()
        ]);

        return response()->json([
            'status' => 500,
            'message' => 'فشل داخلي في الخادم (500). يرجى مراجعة سجلات الأخطاء في المشروع.'
        ], 500);
    }
}


    // ----------------------------------------------------------------------
    // 4. الحذف (Delete Operation) - مع إضافة تسجيل الأخطاء
    // ----------------------------------------------------------------------

    /**
     * حذف مشروع (Destroy Project).
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $project = ProjectDemo::findOrFail($id);
            
            // حذف الملفات والمشروع
            $this->deleteProjectFiles($project);
            $project->delete();

            return response()->json(['status' => 200, 'message' => 'تم حذف المشروع بنجاح.']);
            
        } catch (Throwable $e) {
             // تسجيل الخطأ في حال فشل عملية الحذف (قاعدة البيانات أو التخزين)
            Log::error('فشل في حذف المشروع:', [
                'project_id' => $id,
                'error_message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'status' => 500, 
                'message' => 'فشل داخلي في الخادم (500). يرجى مراجعة سجلات الأخطاء في المشروع.'
            ], 500);
        }
    }

    // ----------------------------------------------------------------------
    // 5. الدوال المساعدة (Private Helper Methods)
    // ----------------------------------------------------------------------

    /**
     * يقوم بتحقق البيانات (Validation).
     *
     * @param \Illuminate\Http\Request $request
     * @param bool $isUpdate
     * @return array
     */
    private function validateProjectData(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'name' => ($isUpdate ? 'sometimes|' : 'required|') . 'string|max:255',
            'description' => 'nullable|string',
            'technologies' => 'nullable|array',
            'technologies.*' => 'string|max:1000',
            // الصور الجديدة يجب أن ترفع بصيغة ملفات
            'images' => 'nullable|array', 
            'images.*' => 'image|max:204800', 
            'cover_image' => 'nullable|image|max:204800',
            'demo_link' => 'nullable|url',
            'type' => 'nullable|string|max:1000',
            'remove_images' => 'nullable|array', // المسارات المراد حذفها
            'remove_images.*' => 'string',
        ]);
    }

    /**
     * رفع صورة واحدة وحفظ مسارها.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $field
     * @param string $path
     * @return string|null
     */
    private function uploadSingleImage(Request $request, string $field, string $path): ?string
    {
        if ($request->hasFile($field)) {
            return $request->file($field)->store($path, 'public');
        }
        return null;
    }

    /**
     * رفع صور متعددة وحفظ مساراتها.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $field
     * @param string $path
     * @return array
     */
    private function uploadMultipleImages(Request $request, string $field, string $path): array
    {
        $paths = [];
        if ($request->hasFile($field)) {
            foreach ($request->file($field) as $file) {
                $paths[] = $file->store($path, 'public');
            }
        }
        return $paths;
    }

    /**
     * تحويل مسارات الصور المخزنة إلى روابط URL كاملة للعرض.
     *
     * @param \App\Models\ProjectDemo $project
     * @return \App\Models\ProjectDemo
     */
    private function formatProjectResponse(ProjectDemo $project): ProjectDemo
    {
        $project = clone $project; // نسخ لتجنب تعديل الكائن الأصلي إن لزم الأمر
        
        // تنسيق صورة الغلاف
        if ($project->cover_image) {
            $project->cover_image_url = url('storage/' . $project->cover_image);
        }

        // تنسيق صور المعرض
        if (is_array($project->images)) {
            $formattedImages = [];
            foreach ($project->images as $path) {
                $formattedImages[] = url('storage/' . $path); 
            }
            $project->images = $formattedImages;
        }

        return $project;
    }


    /**
     * معالجة تحديث صورة الغلاف.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ProjectDemo $project
     * @return void
     */
    private function handleCoverImageUpdate(Request $request, ProjectDemo $project): void
    {
        if ($request->hasFile('cover_image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($project->cover_image && Storage::disk('public')->exists($project->cover_image)) {
                Storage::disk('public')->delete($project->cover_image);
            }
            // رفع الصورة الجديدة
            $project->cover_image = $request->file('cover_image')->store('projects_demo/covers', 'public');
        }
    }

    /**
     * معالجة تحديث صور المعرض (إضافة وحذف).
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ProjectDemo $project
     * @return void
     */
    private function handleGalleryImagesUpdate(Request $request, ProjectDemo $project): void
    {
        // 1. حذف صور محددة
        if ($request->filled('remove_images') && is_array($request->remove_images)) {
            $remainingImages = $project->images ?? [];
            foreach ($request->remove_images as $pathToRemove) {
                if (($key = array_search($pathToRemove, $remainingImages)) !== false) {
                    unset($remainingImages[$key]);
                    // حذف الملف من Storage
                    if (Storage::disk('public')->exists($pathToRemove)) {
                        Storage::disk('public')->delete($pathToRemove);
                    }
                }
            }
            $project->images = array_values($remainingImages);
        }

        // 2. رفع صور جديدة
        if ($request->hasFile('images')) {
            $newPaths = $this->uploadMultipleImages($request, 'images', 'projects_demo/images');
            $project->images = array_merge($project->images ?? [], $newPaths);
        }
    }

    /**
     * حذف جميع الملفات المرتبطة بالمشروع من Storage.
     *
     * @param \App\Models\ProjectDemo $project
     * @return void
     */
    private function deleteProjectFiles(ProjectDemo $project): void
    {
        // حذف صور المعرض
        if (is_array($project->images)) {
            Storage::disk('public')->delete($project->images);
        }

        // حذف صورة الغلاف
        if ($project->cover_image) {
            Storage::disk('public')->delete($project->cover_image);
        }
    }
}