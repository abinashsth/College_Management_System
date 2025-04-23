<?php

namespace App\Http\Controllers;

use App\Models\Mask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaskController extends Controller
{
    /**
     * Display a listing of masks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $masks = Mask::all();
        return view('masks.index', compact('masks'));
    }

    /**
     * Show the form for creating a new mask.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('masks.create');
    }

    /**
     * Store a newly created mask in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'pattern' => 'required|string|max:255',
            'placeholder' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();
        
        $mask = Mask::create($validated);

        return redirect()->route('masks.index')
            ->with('success', 'Mask created successfully.');
    }

    /**
     * Display the specified mask.
     *
     * @param  \App\Models\Mask  $mask
     * @return \Illuminate\Http\Response
     */
    public function show(Mask $mask)
    {
        return view('masks.show', compact('mask'));
    }

    /**
     * Show the form for editing the specified mask.
     *
     * @param  \App\Models\Mask  $mask
     * @return \Illuminate\Http\Response
     */
    public function edit(Mask $mask)
    {
        return view('masks.edit', compact('mask'));
    }

    /**
     * Update the specified mask in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mask  $mask
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mask $mask)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'pattern' => 'required|string|max:255',
            'placeholder' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        
        $mask->update($validated);

        return redirect()->route('masks.index')
            ->with('success', 'Mask updated successfully.');
    }

    /**
     * Remove the specified mask from storage.
     *
     * @param  \App\Models\Mask  $mask
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mask $mask)
    {
        $mask->delete();

        return redirect()->route('masks.index')
            ->with('success', 'Mask deleted successfully.');
    }
} 