<?php

namespace App\Http\Controllers;

use App\Models\Reporting\ReportTemplate;
use App\Models\Reporting\GeneratedReport;
use App\Services\ReportingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    protected $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin') || auth()->user()->hasPermissionTo('view reports')) {
                return $next($request);
            }
            abort(403, 'Unauthorized action.');
        });
    }

    /**
     * Display a listing of available report templates.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type');
        
        $query = ReportTemplate::active()->orderBy('name');
        
        if ($type) {
            $query->ofType($type);
        }
        
        $templates = $query->get();
        
        $recentReports = GeneratedReport::with('template')
            ->where('generated_by', $user->id)
            ->orderBy('generated_at', 'desc')
            ->limit(5)
            ->get();
        
        $reportTypes = [
            'academic' => 'Academic Reports',
            'financial' => 'Financial Reports',
            'attendance' => 'Attendance Reports',
            'examination' => 'Examination Reports',
            'staff' => 'Staff Reports',
            'student' => 'Student Reports',
            'admission' => 'Admission Reports',
            'custom' => 'Custom Reports',
        ];
        
        return view('reports.index', compact('templates', 'recentReports', 'reportTypes', 'type'));
    }

    /**
     * Show the form for generating a new report.
     *
     * @param  int  $templateId
     * @return \Illuminate\Http\Response
     */
    public function create($templateId)
    {
        $template = ReportTemplate::active()->findOrFail($templateId);
        
        // Get parameter definitions
        $parameters = $template->parameters ?? [];
        
        // Get filter definitions
        $filters = $template->filters ?? [];
        
        // Get the options for various parameters
        $parameterOptions = $this->reportingService->getParameterOptions($template);
        
        return view('reports.create', compact('template', 'parameters', 'filters', 'parameterOptions'));
    }

    /**
     * Generate a new report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $templateId
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request, $templateId)
    {
        $template = ReportTemplate::active()->findOrFail($templateId);
        
        // Validate the request based on template parameters
        $rules = $this->reportingService->getValidationRules($template);
        $validatedData = $request->validate($rules);
        
        $user = Auth::user();
        
        // Generate the report
        $report = $this->reportingService->generateReport(
            $template,
            $validatedData,
            $user
        );
        
        if ($report) {
            return redirect()->route('reports.show', $report->id)
                ->with('success', 'Report generated successfully.');
        } else {
            return redirect()->route('reports.index')
                ->with('error', 'Failed to generate report. Please try again.');
        }
    }

    /**
     * Display the specified report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $report = GeneratedReport::with('template', 'generator')->findOrFail($id);
        
        // Parse the report data for display
        $reportData = $this->reportingService->parseReportData($report);
        
        return view('reports.show', compact('report', 'reportData'));
    }

    /**
     * Download a report in the specified format.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request, $id)
    {
        $format = $request->input('format', 'pdf');
        $report = GeneratedReport::with('template')->findOrFail($id);
        
        // Check if this format is supported for this report
        $supportedFormats = $report->template->getExportFormatsArray();
        if (!in_array($format, $supportedFormats)) {
            return redirect()->route('reports.show', $id)
                ->with('error', "Format '{$format}' is not supported for this report.");
        }
        
        // Check if the file already exists
        if ($report->file_path && $report->file_type === $format) {
            return Storage::download($report->file_path, $this->generateFileName($report, $format));
        }
        
        // Generate the file
        $filePath = $this->reportingService->exportReport($report, $format);
        
        if ($filePath) {
            // Update the report with the file path
            $report->file_path = $filePath;
            $report->file_type = $format;
            $report->save();
            
            return Storage::download($filePath, $this->generateFileName($report, $format));
        } else {
            return redirect()->route('reports.show', $id)
                ->with('error', 'Failed to generate report file. Please try again.');
        }
    }

    /**
     * List reports generated by the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function myReports(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type');
        
        $query = GeneratedReport::with('template')
            ->where('generated_by', $user->id)
            ->orderBy('generated_at', 'desc');
        
        if ($type) {
            $query->ofType($type);
        }
        
        $reports = $query->paginate(15);
        
        $reportTypes = [
            'academic' => 'Academic Reports',
            'financial' => 'Financial Reports',
            'attendance' => 'Attendance Reports',
            'examination' => 'Examination Reports',
            'staff' => 'Staff Reports',
            'student' => 'Student Reports',
            'admission' => 'Admission Reports',
            'custom' => 'Custom Reports',
        ];
        
        return view('reports.my-reports', compact('reports', 'reportTypes', 'type'));
    }

    /**
     * Search for reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        
        $query = GeneratedReport::with(['template', 'generator'])
            ->orderBy('generated_at', 'desc');
        
        // Apply search filters
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->input('title') . '%');
        }
        
        if ($request->has('type')) {
            $query->ofType($request->input('type'));
        }
        
        if ($request->has('date_from')) {
            $query->where('report_date', '>=', $request->input('date_from'));
        }
        
        if ($request->has('date_to')) {
            $query->where('report_date', '<=', $request->input('date_to'));
        }
        
        if ($request->has('generated_by')) {
            $query->where('generated_by', $request->input('generated_by'));
        }
        
        // Unless the user is an admin, only show reports they generated
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            $query->where('generated_by', $user->id);
        }
        
        $reports = $query->paginate(15);
        
        return view('reports.search', compact('reports'));
    }

    /**
     * Remove the specified report from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $report = GeneratedReport::findOrFail($id);
        
        // Only the generator or an admin can delete a report
        if ($report->generated_by != $user->id && !$user->hasRole(['Super Admin', 'Admin'])) {
            return redirect()->route('reports.show', $id)
                ->with('error', 'You do not have permission to delete this report.');
        }
        
        // Delete the file if it exists
        if ($report->file_path) {
            Storage::delete($report->file_path);
        }
        
        $report->delete();
        
        return redirect()->route('reports.my-reports')
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Generate a file name for a report download.
     *
     * @param  \App\Models\Reporting\GeneratedReport  $report
     * @param  string  $format
     * @return string
     */
    private function generateFileName($report, $format)
    {
        $title = Str::slug($report->title);
        $date = $report->report_date->format('Y-m-d');
        
        return "{$title}-{$date}.{$format}";
    }

    public function customReport()
    {
        $user = Auth::user();
        
        // Get available metrics and dimensions for custom reports
        $metrics = \App\Models\Analytics\AnalyticsMetric::active()->orderBy('name')->get();
        $dimensions = \App\Models\Analytics\AnalyticsDimension::active()->orderBy('name')->get();
        
        // Get the template for custom reports
        $template = ReportTemplate::where('slug', 'custom-report-builder')->firstOrFail();
        
        return view('reports.custom', compact('metrics', 'dimensions', 'template'));
    }

    /**
     * Generate a custom report based on user-defined parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateCustomReport(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'report_name' => 'required|string|max:255',
            'metrics' => 'required|array',
            'metrics.*' => 'exists:analytics_metrics,id',
            'dimensions' => 'nullable|array',
            'dimensions.*' => 'exists:analytics_dimensions,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'filters' => 'nullable|array',
        ]);
        
        // Get the custom report template
        $template = ReportTemplate::where('slug', 'custom-report-builder')->firstOrFail();
        
        // Generate the report
        $report = $this->reportingService->generateCustomReport(
            $template,
            $validated,
            $user
        );
        
        if ($report) {
            return redirect()->route('reports.show', $report->id)
                ->with('success', 'Custom report generated successfully.');
        } else {
            return redirect()->route('reports.custom')
                ->with('error', 'Failed to generate custom report. Please try again.')
                ->withInput();
        }
    }

    /**
     * Show the comparison report form.
     *
     * @return \Illuminate\Http\Response
     */
    public function comparisonReport()
    {
        $user = Auth::user();
        
        // Get available metrics for comparison
        $metrics = \App\Models\Analytics\AnalyticsMetric::active()
            ->where('comparison_enabled', true)
            ->orderBy('name')
            ->get();
        
        // Get academic years for comparison
        $academicYears = \App\Models\AcademicYear::orderBy('start_date', 'desc')->get();
        
        // Get the comparison report template
        $template = ReportTemplate::where('slug', 'academic-year-comparison')->firstOrFail();
        
        return view('reports.comparison', compact('metrics', 'academicYears', 'template'));
    }

    /**
     * Generate a comparison report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateComparisonReport(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'academic_session_1' => 'required|exists:academic_years,id',
            'academic_session_2' => 'required|exists:academic_years,id|different:academic_session_1',
            'metric_slug' => 'required|exists:analytics_metrics,slug',
            'department_id' => 'nullable|exists:departments,id',
            'program_id' => 'nullable|exists:programs,id',
        ]);
        
        // Get the comparison report template
        $template = ReportTemplate::where('slug', 'academic-year-comparison')->firstOrFail();
        
        // Generate the report
        $report = $this->reportingService->generateReport(
            $template,
            $validated,
            $user
        );
        
        if ($report) {
            return redirect()->route('reports.show', $report->id)
                ->with('success', 'Comparison report generated successfully.');
        } else {
            return redirect()->route('reports.comparison')
                ->with('error', 'Failed to generate comparison report. Please try again.')
                ->withInput();
        }
    }
} 