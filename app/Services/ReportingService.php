<?php

namespace App\Services;

use App\Models\Reporting\ReportTemplate;
use App\Models\Reporting\GeneratedReport;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PDF;
use Maatwebsite\Excel\Facades\Excel;

class ReportingService
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Generate a report based on a template and parameters.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @param User $user
     * @return GeneratedReport|null
     */
    public function generateReport(ReportTemplate $template, array $parameters, User $user)
    {
        // Create the report record
        $report = new GeneratedReport();
        $report->template_id = $template->id;
        $report->title = $this->generateReportTitle($template, $parameters);
        $report->description = $template->description;
        $report->report_type = $template->report_type;
        $report->parameters = $parameters;
        $report->generated_by = $user->id;
        $report->generated_at = Carbon::now();
        
        // Process the report data based on template type
        $reportData = $this->processReportData($template, $parameters);
        if (!$reportData) {
            return null;
        }
        
        $report->report_data = $reportData;
        $report->save();
        
        return $report;
    }

    /**
     * Process report data based on template type and parameters.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @return array|null
     */
    private function processReportData(ReportTemplate $template, array $parameters)
    {
        $data = [];
        
        switch ($template->report_type) {
            case 'academic':
                $data = $this->processAcademicReport($template, $parameters);
                break;
            case 'financial':
                $data = $this->processFinancialReport($template, $parameters);
                break;
            case 'attendance':
                $data = $this->processAttendanceReport($template, $parameters);
                break;
            case 'examination':
                $data = $this->processExaminationReport($template, $parameters);
                break;
            case 'staff':
                $data = $this->processStaffReport($template, $parameters);
                break;
            case 'student':
                $data = $this->processStudentReport($template, $parameters);
                break;
            case 'admission':
                $data = $this->processAdmissionReport($template, $parameters);
                break;
            case 'custom':
                $data = $this->processCustomReport($template, $parameters);
                break;
            case 'comparison':
                $data = $this->processComparisonReport($template, $parameters);
                break;
            default:
                return null;
        }
        
        return $data;
    }

    /**
     * Get validation rules for report parameters.
     *
     * @param ReportTemplate $template
     * @return array
     */
    public function getValidationRules(ReportTemplate $template)
    {
        $rules = [];
        
        $parameters = $template->parameters ?? [];
        
        foreach ($parameters as $param) {
            $name = $param['name'];
            $type = $param['type'] ?? 'string';
            $required = $param['required'] ?? false;
            
            $rule = [];
            
            if ($required) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }
            
            switch ($type) {
                case 'integer':
                    $rule[] = 'integer';
                    break;
                case 'number':
                    $rule[] = 'numeric';
                    break;
                case 'date':
                    $rule[] = 'date';
                    break;
                case 'select':
                    $rule[] = 'string';
                    if (isset($param['options'])) {
                        $rule[] = 'in:' . implode(',', array_keys($param['options']));
                    }
                    break;
                case 'boolean':
                    $rule[] = 'boolean';
                    break;
                case 'array':
                    $rule[] = 'array';
                    break;
                default:
                    $rule[] = 'string';
            }
            
            $rules[$name] = implode('|', $rule);
        }
        
        return $rules;
    }

    /**
     * Generate a report title based on template and parameters.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @return string
     */
    private function generateReportTitle(ReportTemplate $template, array $parameters)
    {
        $title = $template->name;
        
        // Add date range if present
        if (isset($parameters['start_date']) && isset($parameters['end_date'])) {
            $startDate = Carbon::parse($parameters['start_date'])->format('M d, Y');
            $endDate = Carbon::parse($parameters['end_date'])->format('M d, Y');
            $title .= " ({$startDate} - {$endDate})";
        } elseif (isset($parameters['date'])) {
            $date = Carbon::parse($parameters['date'])->format('M d, Y');
            $title .= " ({$date})";
        } elseif (isset($parameters['academic_year_id'])) {
            // Add academic year if available
            $title .= " - Academic Year " . $parameters['academic_year_id'];
        }
        
        return $title;
    }

    /**
     * Parse report data for display.
     *
     * @param GeneratedReport $report
     * @return array
     */
    public function parseReportData(GeneratedReport $report)
    {
        // The report data is already in JSON format, we just need to structure it for display
        $data = $report->report_data;
        
        // Add any additional processing needed for the UI
        $data['generated_at'] = $report->generated_at->format('M d, Y h:i A');
        $data['generator'] = $report->generator->name;
        
        return $data;
    }

    /**
     * Export a report to the specified format.
     *
     * @param GeneratedReport $report
     * @param string $format (pdf, excel, csv)
     * @return string|null File path of the generated file
     */
    public function exportReport(GeneratedReport $report, string $format)
    {
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($report);
            case 'excel':
                return $this->exportToExcel($report);
            case 'csv':
                return $this->exportToCsv($report);
            default:
                return null;
        }
    }

    /**
     * Export report to PDF.
     *
     * @param GeneratedReport $report
     * @return string|null
     */
    private function exportToPdf(GeneratedReport $report)
    {
        $data = $this->parseReportData($report);
        $view = $this->determineReportView($report);
        
        $pdf = PDF::loadView($view, ['report' => $report, 'data' => $data]);
        
        $filename = 'reports/' . Str::slug($report->title) . '-' . $report->id . '.pdf';
        Storage::put($filename, $pdf->output());
        
        return $filename;
    }

    /**
     * Export report to Excel.
     *
     * @param GeneratedReport $report
     * @return string|null
     */
    private function exportToExcel(GeneratedReport $report)
    {
        $data = $this->parseReportData($report);
        
        // Implementation would use Laravel Excel package
        $filename = 'reports/' . Str::slug($report->title) . '-' . $report->id . '.xlsx';
        
        // Example Excel export (simplified)
        // In a real implementation, you'd create a proper Excel export class
        $export = new \App\Exports\ReportExport($report, $data);
        Storage::put($filename, Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX));
        
        return $filename;
    }

    /**
     * Export report to CSV.
     *
     * @param GeneratedReport $report
     * @return string|null
     */
    private function exportToCsv(GeneratedReport $report)
    {
        $data = $this->parseReportData($report);
        
        // Implementation would use Laravel Excel package
        $filename = 'reports/' . Str::slug($report->title) . '-' . $report->id . '.csv';
        
        // Example CSV export (simplified)
        $export = new \App\Exports\ReportExport($report, $data);
        Storage::put($filename, Excel::raw($export, \Maatwebsite\Excel\Excel::CSV));
        
        return $filename;
    }

    /**
     * Determine the correct view for a report.
     *
     * @param GeneratedReport $report
     * @return string
     */
    private function determineReportView(GeneratedReport $report)
    {
        $templateType = $report->template->report_type;
        $templateSlug = $report->template->slug;
        
        // Check for a custom view for this specific template
        $customView = "reports.exports.{$templateSlug}";
        
        if (view()->exists($customView)) {
            return $customView;
        }
        
        // Fall back to a generic view based on the type
        return "reports.exports.{$templateType}";
    }

    /**
     * Get options for report parameters.
     *
     * @param ReportTemplate $template
     * @return array
     */
    public function getParameterOptions(ReportTemplate $template)
    {
        $options = [];
        
        // Retrieve and populate options for various parameter types
        // This would integrate with other services to fetch real data
        
        return $options;
    }

    /**
     * Process academic comparison report.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @return array
     */
    private function processComparisonReport(ReportTemplate $template, array $parameters)
    {
        $data = [
            'title' => $template->name,
            'template_id' => $template->id,
            'parameters' => $parameters,
            'date_generated' => Carbon::now()->toDateTimeString(),
            'comparison_data' => [],
        ];
        
        // Example: Compare academic sessions
        if (isset($parameters['academic_session_1']) && isset($parameters['academic_session_2'])) {
            $data['comparison_data'] = $this->analyticsService->getComparisonData(
                $parameters['metric_slug'] ?? 'student_performance',
                Carbon::parse($parameters['start_date_1'] ?? now()->subYear()),
                Carbon::parse($parameters['end_date_1'] ?? now()),
                Carbon::parse($parameters['start_date_2'] ?? now()->subYears(2)),
                Carbon::parse($parameters['end_date_2'] ?? now()->subYear()),
                [
                    'academic_year_id' => $parameters['academic_year_id'] ?? null,
                    'academic_term_id' => $parameters['academic_term_id'] ?? null,
                    'entity_type' => $parameters['entity_type'] ?? null,
                    'entity_id' => $parameters['entity_id'] ?? null,
                ]
            );
        }
        
        return $data;
    }

    /**
     * Process academic report.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @return array
     */
    private function processAcademicReport(ReportTemplate $template, array $parameters)
    {
        // Implementation for academic reports
        return [];
    }

    /**
     * Process financial report.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @return array
     */
    private function processFinancialReport(ReportTemplate $template, array $parameters)
    {
        // Implementation for financial reports
        return [];
    }

    /**
     * Process attendance report.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @return array
     */
    private function processAttendanceReport(ReportTemplate $template, array $parameters)
    {
        // Implementation for attendance reports
        return [];
    }

    /**
     * Process examination report.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @return array
     */
    private function processExaminationReport(ReportTemplate $template, array $parameters)
    {
        // Implementation for examination reports
        return [];
    }

    /**
     * Process staff report.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @return array
     */
    private function processStaffReport(ReportTemplate $template, array $parameters)
    {
        // Implementation for staff reports
        return [];
    }

    /**
     * Process student report.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @return array
     */
    private function processStudentReport(ReportTemplate $template, array $parameters)
    {
        // Implementation for student reports
        return [];
    }

    /**
     * Process admission report.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @return array
     */
    private function processAdmissionReport(ReportTemplate $template, array $parameters)
    {
        // Implementation for admission reports
        return [];
    }

    /**
     * Process custom report.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @return array
     */
    private function processCustomReport(ReportTemplate $template, array $parameters)
    {
        // Implementation for custom reports
        return [];
    }

    /**
     * Generate a custom report based on user-defined parameters.
     *
     * @param ReportTemplate $template
     * @param array $parameters
     * @param User $user
     * @return GeneratedReport|null
     */
    public function generateCustomReport(ReportTemplate $template, array $parameters, User $user)
    {
        // Create the report record
        $report = new GeneratedReport();
        $report->template_id = $template->id;
        $report->title = $parameters['report_name'];
        $report->description = "Custom report created by {$user->name}";
        $report->report_type = 'custom';
        $report->parameters = $parameters;
        $report->generated_by = $user->id;
        $report->generated_at = Carbon::now();
        
        // Get the selected metrics
        $metricIds = $parameters['metrics'] ?? [];
        $metrics = \App\Models\Analytics\AnalyticsMetric::whereIn('id', $metricIds)->get();
        
        // Get the selected dimensions
        $dimensionIds = $parameters['dimensions'] ?? [];
        $dimensions = \App\Models\Analytics\AnalyticsDimension::whereIn('id', $dimensionIds)->get();
        
        // Prepare filters for the analytics query
        $filters = [
            'start_date' => $parameters['start_date'] ?? null,
            'end_date' => $parameters['end_date'] ?? null,
        ];
        
        // Add custom filters if provided
        if (isset($parameters['filters']) && is_array($parameters['filters'])) {
            foreach ($parameters['filters'] as $key => $value) {
                if (!empty($value)) {
                    $filters[$key] = $value;
                }
            }
        }
        
        // Build the report data
        $reportData = [
            'title' => $parameters['report_name'],
            'parameters' => $parameters,
            'date_generated' => Carbon::now()->toDateTimeString(),
            'metrics' => [],
            'dimensions' => [],
            'filters' => $filters,
            'data' => [],
            'rows' => [],
            'headers' => []
        ];
        
        // Process each metric
        foreach ($metrics as $metric) {
            $reportData['metrics'][] = [
                'id' => $metric->id,
                'name' => $metric->name,
                'slug' => $metric->slug,
                'data_type' => $metric->data_type,
                'display_options' => $metric->display_options,
            ];
            
            // If dimensions are selected, get data by dimensions
            if ($dimensions->isNotEmpty()) {
                foreach ($dimensions as $dimension) {
                    $dimensionData = $this->analyticsService->getDataByDimension($metric->slug, $dimension->slug, $filters);
                    $reportData['data'][$metric->slug][$dimension->slug] = $dimensionData;
                    
                    // Build rows for the export
                    foreach ($dimensionData as $value => $count) {
                        $reportData['rows'][] = [
                            'Metric' => $metric->name,
                            'Dimension' => $dimension->name,
                            'Value' => $value,
                            'Count/Amount' => $count,
                        ];
                    }
                }
            } else {
                // Otherwise, get aggregated data
                $aggregatedValue = $this->analyticsService->getAggregatedData([$metric->slug], $filters);
                $reportData['data'][$metric->slug]['aggregated'] = $aggregatedValue;
                
                // Add to rows for export
                $reportData['rows'][] = [
                    'Metric' => $metric->name,
                    'Value' => $aggregatedValue->first()['value'] ?? 0,
                ];
            }
        }
        
        // Add dimension information
        foreach ($dimensions as $dimension) {
            $reportData['dimensions'][] = [
                'id' => $dimension->id,
                'name' => $dimension->name,
                'slug' => $dimension->slug,
            ];
        }
        
        // Set headers for exports
        $reportData['headers'] = $dimensions->isNotEmpty() 
            ? ['Metric', 'Dimension', 'Value', 'Count/Amount']
            : ['Metric', 'Value'];
        
        // Save the report data
        $report->report_data = $reportData;
        $report->save();
        
        return $report;
    }
} 