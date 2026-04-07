<?php

namespace App\Http\Controllers;

use App\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SocialLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(SocialLink::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'site_info_id' => 'required|exists:site_info,id',
                'github' => 'nullable|url',
                'linkedin' => 'nullable|url',
                'facebook' => 'nullable|url',
                'twitter' => 'nullable|url',
                'instagram' => 'nullable|url',
                'whatsapp' => 'nullable|url',
                'youtube' => 'nullable|url',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $socialLink = SocialLink::create($validated);
        return response()->json($socialLink, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(SocialLink $socialLink)
    {
        return response()->json($socialLink);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SocialLink $socialLink)
    {
        try {
            $validated = $request->validate([
                'site_info_id' => 'sometimes|required|exists:site_info,id',
                'github' => 'nullable|url',
                'linkedin' => 'nullable|url',
                'facebook' => 'nullable|url',
                'twitter' => 'nullable|url',
                'instagram' => 'nullable|url',
                'whatsapp' => 'nullable|url',
                'youtube' => 'nullable|url',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $socialLink->update($validated);
        return response()->json($socialLink);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SocialLink $socialLink)
    {
        $socialLink->delete();
        return response()->json(null, 204);
    }
}
