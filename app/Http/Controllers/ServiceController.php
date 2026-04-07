<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Service::all());
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    }

    if ($request->hasFile('icon')) {
        $path = $request->file('icon')->store('services', 'public');
        $validated['icon'] = ('/storage/' . $path);
    }

    $service = Service::create($validated);
    return response()->json($service, 201);
}

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return response()->json($service);
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, Service $service)
{
    try {
        $validated = $request->validate([
            'icon' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
        ]);
    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    }

    if ($request->hasFile('icon')) {
        $path = $request->file('icon')->store('services', 'public');
        $validated['icon'] = ('/storage/' . $path);
    }

    $service->update($validated);
    return response()->json($service);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json(null, 204);
    }
}
