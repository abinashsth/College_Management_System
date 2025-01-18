<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view classes']);
    }

    public function index()
    {
        $classes = Classes::withCount('students')->paginate(10);
        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
        ]);

        Classes::create([
            'class_name' => $request->class_name,
            'section' => $request->section,
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully');
    }

    public function edit(Classes $class)
    {
        return view('classes.edit', compact('class'));
    }

    public function update(Request $request, Classes $class)
    {
        $request->validate([
            'class_name' => 'required|string|max:255',
            'section' => 'nullable|string|max:255',
        ]);

        $class->update([
            'class_name' => $request->class_name,
            'section' => $request->section,
        ]);

        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully');
    }

    public function destroy(Classes $class)
    {
        if ($class->students()->count() > 0) {
            return redirect()->route('classes.index')
                ->with('error', 'Cannot delete class with students. Please remove students first.');
        }

        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully');
    }
}
