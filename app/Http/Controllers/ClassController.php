<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public static function middleware(): array 
    {
        return ['role:User'];
    }

    public function index()
    {
        $classes = ClassModel::with('students')->paginate(10);
        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        return view('classes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
            'section' => 'nullable|string|max:50'
        ]);

        ClassModel::create($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Class created successfully');
    }

    public function edit(ClassModel $class)
    {
        return view('classes.edit', compact('class'));
    }

    public function update(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
            'section' => 'nullable|string|max:50'
        ]);

        $class->update($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Class updated successfully');
    }

    public function destroy(ClassModel $class)
    {
        $class->delete();
        return redirect()->route('classes.index')
            ->with('success', 'Class deleted successfully');
    }
}
