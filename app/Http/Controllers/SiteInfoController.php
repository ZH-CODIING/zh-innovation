<?php

namespace App\Http\Controllers;

use App\Models\SiteInfo;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage; // Don't forget to add this line

class SiteInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(SiteInfo::with('socialLinks')->get());
    }

    /**
     * Store a newly created resource in storage.
     * Updated to support image uploads.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'logo' => 'nullable|image|max:2048', // Changed to image validation
                'cv' => 'nullable|mimes:pdf|max:10240', // Changed to PDF validation
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'job_title' => 'nullable|string|max:255',
                'experience_years' => 'nullable|integer',
                'hero_section_image' => 'nullable|image|max:2048', // Changed to image validation
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'footer_description' => 'nullable|string',
                'copyright_text' => 'nullable|string|max:255',
                // Social links can be handled in a separate request or nested here
                'social_links.github' => 'nullable|url',
                'social_links.linkedin' => 'nullable|url',
                'social_links.facebook' => 'nullable|url',
                'social_links.twitter' => 'nullable|url',
                'social_links.instagram' => 'nullable|url',
                'social_links.whatsapp' => 'nullable|url',
                'social_links.youtube' => 'nullable|url',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $dataToCreate = $request->except(['_token', 'social_links']);

        // Handle file uploads for store method
        if ($request->hasFile('logo')) {
            $dataToCreate['logo'] = Storage::disk('public')->put('site_info/logo', $request->file('logo'));
            $dataToCreate['logo'] = Storage::url($dataToCreate['logo']); // Get public URL
        }

        if ($request->hasFile('cv')) {
            $dataToCreate['cv'] = Storage::disk('public')->put('site_info/cv', $request->file('cv'));
            $dataToCreate['cv'] = Storage::url($dataToCreate['cv']); // Get public URL
        }

        if ($request->hasFile('hero_section_image')) {
            $dataToCreate['hero_section_image'] = Storage::disk('public')->put('site_info/hero', $request->file('hero_section_image'));
            $dataToCreate['hero_section_image'] = Storage::url($dataToCreate['hero_section_image']); // Get public URL
        }

        $siteInfo = SiteInfo::create($dataToCreate);

        if (isset($validated['social_links'])) {
            $siteInfo->socialLinks()->create($validated['social_links']);
        }

        return response()->json($siteInfo->load('socialLinks'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SiteInfo $siteInfo)
    {
        return response()->json($siteInfo->load('socialLinks'));
    }

    /**
     * Update the specified resource in storage.
     * Use POST for update and support partial updates.
     */
    public function update(Request $request, SiteInfo $siteInfo)
    {
        try {
            $validated = $request->validate([
                'logo' => 'nullable|image|max:2048', // Allow image upload
                'cv' => 'nullable|mimes:pdf|max:10240', // Allow PDF upload
                'name' => 'nullable|string|max:255', // Changed to nullable
                'description' => 'nullable|string',
                'job_title' => 'nullable|string|max:255',
                'experience_years' => 'nullable|integer',
                'hero_section_image' => 'nullable|image|max:2048', // Allow image upload
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'footer_description' => 'nullable|string',
                'copyright_text' => 'nullable|string|max:255',
                'social_links.github' => 'nullable|url',
                'social_links.linkedin' => 'nullable|url',
                'social_links.facebook' => 'nullable|url',
                'social_links.twitter' => 'nullable|url',
                'social_links.instagram' => 'nullable|url',
                'social_links.whatsapp' => 'nullable|url',
                'social_links.youtube' => 'nullable|url',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // Use $request->except() to get only the fields that were sent in the request,
        // and exclude any internal Laravel fields or nested arrays.
        $dataToUpdate = $request->except(['_token', '_method', 'social_links']);

        // Handle file uploads for update method
        // Always delete old file if a new one is uploaded to avoid clutter
        if ($request->hasFile('logo')) {
            if ($siteInfo->logo) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $siteInfo->logo));
            }
            $dataToUpdate['logo'] = Storage::disk('public')->put('site_info/logo', $request->file('logo'));
            $dataToUpdate['logo'] = Storage::url($dataToUpdate['logo']);
        }

        if ($request->hasFile('cv')) {
            if ($siteInfo->cv) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $siteInfo->cv));
            }
            $dataToUpdate['cv'] = Storage::disk('public')->put('site_info/cv', $request->file('cv'));
            $dataToUpdate['cv'] = Storage::url($dataToUpdate['cv']);
        }

        if ($request->hasFile('hero_section_image')) {
            if ($siteInfo->hero_section_image) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $siteInfo->hero_section_image));
            }
            $dataToUpdate['hero_section_image'] = Storage::disk('public')->put('site_info/hero', $request->file('hero_section_image'));
            $dataToUpdate['hero_section_image'] = Storage::url($dataToUpdate['hero_section_image']);
        }

        // Fill the model with only the provided data and save it.
        // This ensures partial updates.
        $siteInfo->fill($dataToUpdate);
        $siteInfo->save();

        if (isset($validated['social_links'])) {
            $siteInfo->socialLinks()->updateOrCreate([], $validated['social_links']);
        }

        return response()->json($siteInfo->load('socialLinks'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SiteInfo $siteInfo)
    {
        // Delete associated files when the SiteInfo record is deleted
        if ($siteInfo->logo) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $siteInfo->logo));
        }
        if ($siteInfo->cv) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $siteInfo->cv));
        }
        if ($siteInfo->hero_section_image) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $siteInfo->hero_section_image));
        }

        $siteInfo->delete();
        return response()->json(null, 204);
    }
}