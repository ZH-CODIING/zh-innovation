<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage; // Don't forget to add this line

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Course::all());
    }

    /**
     * Store a newly created resource in storage.
     * Updated to support image uploads for 'image' field, 'video' remains a URL.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048', // Changed to image validation
                'video' => 'nullable|url', // Remains a URL
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $dataToCreate = $request->except(['_token']); // Exclude internal fields

        // Handle file upload for 'image'
        if ($request->hasFile('image')) {
            $dataToCreate['image'] = Storage::disk('public')->put('courses/images', $request->file('image'));
            $dataToCreate['image'] = Storage::url($dataToCreate['image']); // Get public URL
        }

        $course = Course::create($dataToCreate);
        return response()->json($course, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return response()->json($course);
    }

    /**
     * Update the specified resource in storage.
     * Use POST for update, support partial updates, handle image uploads, 'video' remains a URL.
     */
    public function update(Request $request, Course $course)
    {
        try {
            $validated = $request->validate([
                'title' => 'nullable|string|max:255', // Changed to nullable
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048', // Allow image upload
                'video' => 'nullable|url', // Remains a URL
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $dataToUpdate = $request->except(['_token', '_method']); // Exclude internal fields

        // Handle file upload for 'image'
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($course->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $course->image));
            }
            $dataToUpdate['image'] = Storage::disk('public')->put('courses/images', $request->file('image'));
            $dataToUpdate['image'] = Storage::url($dataToUpdate['image']); // Get public URL
        }

        // Fill the model with only the provided data and save it.
        // This ensures partial updates.
        $course->fill($dataToUpdate);
        $course->save();

        return response()->json($course);
    }

    /**
     * Remove the specified resource from storage.
     * Added logic to delete the associated image.
     */
    public function destroy(Course $course)
    {
        // Delete associated image file from storage
        if ($course->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $course->image));
        }

        $course->delete();
        return response()->json(null, 204);
    }
}