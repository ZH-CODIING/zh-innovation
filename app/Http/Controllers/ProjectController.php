<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage; // Don't forget to add this line

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Project::all());
    }

    /**
     * Store a newly created resource in storage.
     * Updated to support image uploads for 'image' field.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048', // Changed to image validation
                'github_link' => 'nullable|url',
                'live_link' => 'nullable|url',
                'project_type' => 'nullable|string|max:255',
                'duration' => 'nullable|string|max:255',
                'end_date' => 'nullable|date',
                'location' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $dataToCreate = $request->except(['_token']); // Exclude internal fields

        // Handle file upload for 'image'
        if ($request->hasFile('image')) {
            $dataToCreate['image'] = Storage::disk('public')->put('projects/images', $request->file('image'));
            $dataToCreate['image'] = Storage::url($dataToCreate['image']); // Get public URL
        }

        $project = Project::create($dataToCreate);
        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return response()->json($project);
    }

    /**
     * Update the specified resource in storage.
     * Use POST for update, support partial updates, and handle image uploads.
     */
    public function update(Request $request, Project $project)
    {
        try {
            $validated = $request->validate([
                'title' => 'nullable|string|max:255', // Changed to nullable
                'description' => 'nullable|string',
                'image' => 'nullable|image|max:2048', // Allow image upload
                'github_link' => 'nullable|url',
                'live_link' => 'nullable|url',
                'project_type' => 'nullable|string|max:255',
                'duration' => 'nullable|string|max:255',
                'end_date' => 'nullable|date',
                'location' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $dataToUpdate = $request->except(['_token', '_method']); // Exclude internal fields

        // Handle file upload for 'image'
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($project->image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $project->image));
            }
            $dataToUpdate['image'] = Storage::disk('public')->put('projects/images', $request->file('image'));
            $dataToUpdate['image'] = Storage::url($dataToUpdate['image']); // Get public URL
        }

        // Fill the model with only the provided data and save it.
        // This ensures partial updates.
        $project->fill($dataToUpdate);
        $project->save();

        return response()->json($project);
    }

    /**
     * Remove the specified resource from storage.
     * Added logic to delete the associated image.
     */
    public function destroy(Project $project)
    {
        // Delete associated image file from storage
        if ($project->image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $project->image));
        }

        $project->delete();
        return response()->json(null, 204);
    }
}