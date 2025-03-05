<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ExamType;
use App\Models\AcademicSession;
use App\Services\ResultCompilationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ResultCompilationController extends Controller
{
    protected $resultCompilationService;

    public function __construct(ResultCompilationService $resultCompilationService)
    {
        $this->resultCompilationService = $resultCompilationService;
        $this->middleware('auth');
    }

    public function index()
    {
        if (!Gate::allows('manage exams')) {
            abort(403);
        }

        $classes = Classes::where('is_active', true)->get();
        $examTypes = ExamType::where('status', true)->get();
        $academicSessions = AcademicSession::where('is_active', true)->get();

        return view('results.compilation.index', compact('classes', 'examTypes', 'academicSessions'));
    }

    public function compileTerminalResults(Request $request)
    {
        if (!Gate::allows('manage exams')) {
            abort(403);
        }

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'exam_type_id' => 'required|exists:exam_types,id',
            'academic_session_id' => 'required|exists:academic_sessions,id'
        ]);

        $class = Classes::findOrFail($request->class_id);
        $examType = ExamType::findOrFail($request->exam_type_id);
        $academicSession = AcademicSession::findOrFail($request->academic_session_id);

        try {
            $results = $this->resultCompilationService->compileTerminalResults(
                $class,
                $examType,
                $academicSession
            );

            return redirect()
                ->route('results.compilation.show-terminal', [
                    'class' => $class->id,
                    'examType' => $examType->id,
                    'academicSession' => $academicSession->id
                ])
                ->with('success', 'Terminal results compiled successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to compile terminal results: ' . $e->getMessage());
        }
    }

    public function showTerminalResults(Classes $class, ExamType $examType, AcademicSession $academicSession)
    {
        if (!Gate::allows('view exams')) {
            abort(403);
        }

        $results = $class->terminalMarksLedgers()
            ->where('exam_type_id', $examType->id)
            ->where('academic_session_id', $academicSession->id)
            ->orderBy('rank')
            ->with(['student', 'examType'])
            ->get();

        return view('results.compilation.show-terminal', compact('results', 'class', 'examType', 'academicSession'));
    }

    public function compileFinalResults(Request $request)
    {
        if (!Gate::allows('manage exams')) {
            abort(403);
        }

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_session_id' => 'required|exists:academic_sessions,id'
        ]);

        $class = Classes::findOrFail($request->class_id);
        $academicSession = AcademicSession::findOrFail($request->academic_session_id);

        try {
            $results = $this->resultCompilationService->compileFinalResults(
                $class,
                $academicSession
            );

            return redirect()
                ->route('results.compilation.show-final', [
                    'class' => $class->id,
                    'academicSession' => $academicSession->id
                ])
                ->with('success', 'Final results compiled successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to compile final results: ' . $e->getMessage());
        }
    }

    public function showFinalResults(Classes $class, AcademicSession $academicSession)
    {
        if (!Gate::allows('view exams')) {
            abort(403);
        }

        $results = $class->finalGradeSheets()
            ->where('academic_session_id', $academicSession->id)
            ->orderBy('rank')
            ->with('student')
            ->get();

        return view('results.compilation.show-final', compact('results', 'class', 'academicSession'));
    }

    public function downloadTerminalResults(Classes $class, ExamType $examType, AcademicSession $academicSession)
    {
        if (!Gate::allows('view exams')) {
            abort(403);
        }

        $results = $class->terminalMarksLedgers()
            ->where('exam_type_id', $examType->id)
            ->where('academic_session_id', $academicSession->id)
            ->orderBy('rank')
            ->with(['student', 'examType'])
            ->get();

        $filename = sprintf(
            'terminal_results_%s_%s_%s.pdf',
            $class->class_name,
            $examType->name,
            $academicSession->name
        );

        return view('results.compilation.terminal-results-pdf', compact('results', 'class', 'examType', 'academicSession'));
    }

    public function downloadFinalResults(Classes $class, AcademicSession $academicSession)
    {
        if (!Gate::allows('view exams')) {
            abort(403);
        }

        $results = $class->finalGradeSheets()
            ->where('academic_session_id', $academicSession->id)
            ->orderBy('rank')
            ->with('student')
            ->get();

        $filename = sprintf(
            'final_results_%s_%s.pdf',
            $class->class_name,
            $academicSession->name
        );

        return view('results.compilation.final-results-pdf', compact('results', 'class', 'academicSession'));
    }
} 