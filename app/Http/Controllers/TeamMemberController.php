<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    // جلب جميع أعضاء الفريق
    public function index()
    {
        return response()->json(TeamMember::all(), 200);
    }

    // إنشاء عضو جديد
  public function store(Request $request)
{
    $request->validate([
        'name'        => 'required|string|max:255',
        'role'        => 'required|string|max:255',
        'description' => 'nullable|string',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // دعم الصور فقط
    ]);

    $data = $request->all();

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('team_members', 'public');
        $data['image'] = url('storage/' . $imagePath); // حفظ رابط الصورة
    }

    $teamMember = TeamMember::create($data);

    return response()->json($teamMember, 201);
}


    // جلب بيانات عضو معين
    public function show($id)
    {
        $teamMember = TeamMember::find($id);

        if (!$teamMember) {
            return response()->json(['message' => 'العضو غير موجود'], 404);
        }

        return response()->json($teamMember, 200);
    }

    // تحديث بيانات عضو
public function update(Request $request, $id)
{
    $teamMember = TeamMember::find($id);

    if (!$teamMember) {
        return response()->json(['message' => 'العضو غير موجود'], 404);
    }

    $request->validate([
        'name'        => 'sometimes|string|max:255',
        'role'        => 'sometimes|string|max:255',
        'description' => 'sometimes|string',
        'image'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $data = $request->except('image'); // استثناء الصورة لحين التأكد من وجودها

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('team_members', 'public');
        $data['image'] = url('storage/' . $imagePath);
    }

    $teamMember->update($data);

    return response()->json($teamMember, 200);
}


    // حذف عضو من الفريق
    public function destroy($id)
    {
        $teamMember = TeamMember::find($id);

        if (!$teamMember) {
            return response()->json(['message' => 'العضو غير موجود'], 404);
        }

        $teamMember->delete();

        return response()->json(['message' => 'تم حذف العضو بنجاح'], 200);
    }
}
