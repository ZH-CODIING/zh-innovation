<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * عرض جميع الباقات
     */
    public function index()
    {
        $packages = Package::latest()->get();

        return response()->json([
            'status' => 200,
            'message' => 'تم جلب الباقات',
            'data' => $packages
        ]);
    }

    /**
     * إنشاء باقة جديدة
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string',
        ]);

        $package = Package::create($validated);

        return response()->json([
            'status' => 201,
            'message' => 'تم إنشاء الباقة',
            'data' => $package
        ], 201);
    }

    /**
     * عرض باقة واحدة
     */
    public function show($id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status' => 404,
                'message' => 'الباقة غير موجودة'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'تم جلب بيانات الباقة',
            'data' => $package
        ]);
    }

    /**
     * تحديث باقة
     */
    public function update(Request $request, $id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status' => 404,
                'message' => 'الباقة غير موجودة'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'features' => 'nullable|array',
            'features.*' => 'string',
        ]);

        $package->update($validated);

        return response()->json([
            'status' => 200,
            'message' => 'تم تحديث الباقة',
            'data' => $package
        ]);
    }

    /**
     * حذف باقة
     */
    public function destroy($id)
    {
        $package = Package::find($id);

        if (!$package) {
            return response()->json([
                'status' => 404,
                'message' => 'الباقة غير موجودة'
            ], 404);
        }

        $package->delete();

        return response()->json([
            'status' => 200,
            'message' => 'تم حذف الباقة'
        ]);
    }
}
