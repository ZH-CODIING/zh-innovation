<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage; // Don't forget to add this line

class ExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Experience::all());
    }

    /**
     * Store a newly created resource in storage.
     * Updated to support image uploads for company_logo.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'job_title' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'start_year' => 'nullable|string|max:255',
                'end_year' => 'nullable|string|max:255',
                'company_logo' => 'nullable|image|max:2048', // Changed to image validation
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $dataToCreate = $request->except(['_token']); // Exclude internal fields

        // Handle file upload for company_logo
        if ($request->hasFile('company_logo')) {
            $dataToCreate['company_logo'] = Storage::disk('public')->put('experiences/logos', $request->file('company_logo'));
            $dataToCreate['company_logo'] = Storage::url($dataToCreate['company_logo']); // Get public URL
        }

        $experience = Experience::create($dataToCreate);
        return response()->json($experience, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Experience $experience)
    {
        return response()->json($experience);
    }

    /**
     * Update the specified resource in storage.
     * Use POST for update, support partial updates, and handle image uploads.
     */
    public function update(Request $request, Experience $experience)
    {
        try {
            $validated = $request->validate([
                'job_title' => 'nullable|string|max:255', // Changed to nullable
                'company_name' => 'nullable|string|max:255', // Changed to nullable
                'start_year' => 'nullable|string|max:255',
                'end_year' => 'nullable|string|max:255',
                'company_logo' => 'nullable|image|max:2048', // Allow image upload
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $dataToUpdate = $request->except(['_token', '_method']); // Exclude internal fields

        // Handle file upload for company_logo
        if ($request->hasFile('company_logo')) {
            // Delete old logo if it exists
            if ($experience->company_logo) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $experience->company_logo));
            }
            $dataToUpdate['company_logo'] = Storage::disk('public')->put('experiences/logos', $request->file('company_logo'));
            $dataToUpdate['company_logo'] = Storage::url($dataToUpdate['company_logo']); // Get public URL
        }

        // Fill the model with only the provided data and save it.
        // This ensures partial updates.
        $experience->fill($dataToUpdate);
        $experience->save();

        return response()->json($experience);
    }

    /**
     * Remove the specified resource from storage.
     * Added logic to delete the associated image.
     */
    public function destroy(Experience $experience)
    {
        // Delete associated company_logo file from storage
        if ($experience->company_logo) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $experience->company_logo));
        }

        $experience->delete();
        return response()->json(null, 204);
    }
}