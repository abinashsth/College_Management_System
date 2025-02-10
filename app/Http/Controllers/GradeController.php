<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::all();
        return view('exams.grades.index', compact('grades'));
    }

    public function create()
    {
        return view('exams.grades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:10|unique:grades',
            'point' => 'required|numeric|min:0|max:4',
            'mark_from' => 'required|numeric|min:0|max:100',
            'mark_to' => 'required|numeric|min:0|max:100|gt:mark_from',
            'comment' => 'nullable|string|max:255'
        ]);

        Grade::create($request->all());
        return redirect()->route('grades.index')->with('success', 'Grade created successfully');
    }

    public function edit(Grade $grade)
    {
        return view('exams.grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        $request->validate([
            'name' => 'required|string|max:10|unique:grades,name,' . $grade->id,
            'point' => 'required|numeric|min:0|max:4',
            'mark_from' => 'required|numeric|min:0|max:100',
            'mark_to' => 'required|numeric|min:0|max:100|gt:mark_from',
            'comment' => 'nullable|string|max:255'
        ]);

        $grade->update($request->all());
        return redirect()->route('grades.index')->with('success', 'Grade updated successfully');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return redirect()->route('grades.index')->with('success', 'Grade deleted successfully');
    }
}
