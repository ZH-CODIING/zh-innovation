<?php

namespace App\Http\Controllers;
use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    // عرض كل اللينكات
    public function index()
    {
        return response()->json(Link::all());
    }

    // حفظ لينك جديد
    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
            'title' => 'nullable|string|max:255',
        ]);

        $link = Link::create([
            'title' => $request->title,
            'url'   => $request->url,
        ]);

        return response()->json($link, 201);
    }

    // عرض لينك واحد
    public function show($id)
    {
        $link = Link::findOrFail($id);
        return response()->json($link);
    }

// تحديث لينك موجود
public function update(Request $request, $id)
{
    $link = Link::findOrFail($id);

    $request->validate([
        'url'   => 'sometimes|required|url',
        'title' => 'nullable|string|max:255',
    ]);

    // نستخدم fill ثم save للتأكد من تحديث القيم الموجودة فقط
    $link->fill($request->only(['url', 'title']));
    
    if ($link->isDirty()) { // هل تم تغيير أي شيء فعلاً؟
        $link->save();
    }

    return response()->json([
        'message' => 'تم التحديث بنجاح',
        'data'    => $link
    ]);
}
    // حذف لينك
    public function destroy($id)
    {
        $link = Link::findOrFail($id);
        $link->delete();
        return response()->json(['message' => 'تم الحذف بنجاح']);
    }
}
