<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Skill::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'percentage' => 'nullable|integer|min:0|max:100',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $skill = Skill::create($validated);
        return response()->json($skill, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Skill $skill)
    {
        return response()->json($skill);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Skill $skill)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'percentage' => 'nullable|integer|min:0|max:100',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $skill->update($validated);
        return response()->json($skill);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();
        return response()->json(null, 204);
    }
}
