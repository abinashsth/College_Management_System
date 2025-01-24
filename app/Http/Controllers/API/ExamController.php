<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    public function index(): JsonResponse
    {
        $exams = Exam::with(['class', 'students'])->get();
        return response()->json(['data' => $exams]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:255',
            'total_marks' => 'required|integer|min:0',
            'passing_marks' => 'required|integer|min:0|lte:total_marks',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exam = Exam::create($request->all());
        return response()->json(['data' => $exam, 'message' => 'Exam created successfully'], 201);
    }

    public function show(Exam $exam): JsonResponse
    {
        $exam->load(['class', 'students']);
        return response()->json(['data' => $exam]);
    }

    public function update(Request $request, Exam $exam): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'exam_date' => 'date',
            'class_id' => 'exists:classes,id',
            'subject' => 'string|max:255',
            'total_marks' => 'integer|min:0',
            'passing_marks' => 'integer|min:0|lte:total_marks',
            'status' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exam->update($request->all());
        return response()->json(['data' => $exam, 'message' => 'Exam updated successfully']);
    }

    public function destroy(Exam $exam): JsonResponse
    {
        $exam->delete();
        return response()->json(['message' => 'Exam deleted successfully']);
    }

    public function storeResults(Request $request, Exam $exam): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'results' => 'required|array',
            'results.*.student_id' => 'required|exists:students,id',
            'results.*.grade' => 'required|numeric|min:0|max:100',
            'results.*.remarks' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        foreach ($request->results as $result) {
            $exam->students()->syncWithoutDetaching([
                $result['student_id'] => [
                    'grade' => $result['grade'],
                    'remarks' => $result['remarks'] ?? null
                ]
            ]);
        }

        return response()->json(['message' => 'Exam results stored successfully']);
    }
}
