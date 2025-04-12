<?php

namespace App\Services;

use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Dashboard\DashboardWidget;
use App\Models\Dashboard\DashboardWidgetInstance;
use App\Models\Dashboard\UserDashboard;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardService
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Create a default dashboard for a user.
     *
     * @param User $user
     * @return UserDashboard
     */
    public function createDefaultDashboard(User $user)
    {
        $dashboard = new UserDashboard();
        $dashboard->user_id = $user->id;
        $dashboard->name = 'Default Dashboard';
        $dashboard->slug = 'default-' . Str::random(5);
        $dashboard->description = 'Your default dashboard';
        $dashboard->is_default = true;
        $dashboard->is_shared = false;
        $dashboard->layout = ['layout' => 'grid'];
        $dashboard->save();

        // Get role-specific default widgets
        $widgetConfigs = $this->getDefaultWidgetsForUser($user);

        // Add the widgets to the dashboard
        foreach ($widgetConfigs as $index => $config) {
            $widget = DashboardWidget::where('slug', $config['widget_slug'])->first();
            
            if ($widget) {
                $instance = new DashboardWidgetInstance();
                $instance->dashboard_id = $dashboard->id;
                $instance->widget_id = $widget->id;
                $instance->position_x = $config['position']['x'];
                $instance->position_y = $config['position']['y'];
                $instance->width = $config['position']['w'];
                $instance->height = $config['position']['h'];
                $instance->instance_config = $config['config'] ?? null;
                $instance->save();
            }
        }

        return $dashboard;
    }

    /**
     * Get default widgets for a user based on their role.
     *
     * @param User $user
     * @return array
     */
    protected function getDefaultWidgetsForUser(User $user)
    {
        $widgetConfigs = [];

        // Common widgets for all users
        $widgetConfigs[] = [
            'widget_slug' => 'quick-stats',
            'position' => ['x' => 0, 'y' => 0, 'w' => 12, 'h' => 1],
            'config' => null,
        ];

        // Role-specific widgets
        if ($user->hasRole('Super Admin')) {
            $widgetConfigs[] = [
                'widget_slug' => 'system-health',
                'position' => ['x' => 0, 'y' => 1, 'w' => 6, 'h' => 2],
                'config' => null,
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'user-activity',
                'position' => ['x' => 6, 'y' => 1, 'w' => 6, 'h' => 2],
                'config' => null,
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'student-enrollment-trends',
                'position' => ['x' => 0, 'y' => 3, 'w' => 12, 'h' => 3],
                'config' => ['chart_type' => 'line', 'time_range' => 'year'],
            ];
        } elseif ($user->hasRole('Admin')) {
            $widgetConfigs[] = [
                'widget_slug' => 'recent-activities',
                'position' => ['x' => 0, 'y' => 1, 'w' => 6, 'h' => 2],
                'config' => null,
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'student-distribution',
                'position' => ['x' => 6, 'y' => 1, 'w' => 6, 'h' => 2],
                'config' => ['chart_type' => 'doughnut'],
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'fees-collection',
                'position' => ['x' => 0, 'y' => 3, 'w' => 6, 'h' => 3],
                'config' => ['time_range' => 'month'],
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'upcoming-events',
                'position' => ['x' => 6, 'y' => 3, 'w' => 6, 'h' => 3],
                'config' => null,
            ];
        } elseif ($user->hasRole('Teacher')) {
            $widgetConfigs[] = [
                'widget_slug' => 'my-classes',
                'position' => ['x' => 0, 'y' => 1, 'w' => 6, 'h' => 2],
                'config' => null,
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'attendance-summary',
                'position' => ['x' => 6, 'y' => 1, 'w' => 6, 'h' => 2],
                'config' => null,
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'exam-schedule',
                'position' => ['x' => 0, 'y' => 3, 'w' => 6, 'h' => 3],
                'config' => null,
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'student-performance',
                'position' => ['x' => 6, 'y' => 3, 'w' => 6, 'h' => 3],
                'config' => ['chart_type' => 'bar'],
            ];
        } elseif ($user->hasRole('Accountant')) {
            $widgetConfigs[] = [
                'widget_slug' => 'revenue-summary',
                'position' => ['x' => 0, 'y' => 1, 'w' => 6, 'h' => 2],
                'config' => ['time_range' => 'month'],
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'payment-status',
                'position' => ['x' => 6, 'y' => 1, 'w' => 6, 'h' => 2],
                'config' => ['chart_type' => 'pie'],
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'outstanding-fees',
                'position' => ['x' => 0, 'y' => 3, 'w' => 6, 'h' => 3],
                'config' => null,
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'expense-breakdown',
                'position' => ['x' => 6, 'y' => 3, 'w' => 6, 'h' => 3],
                'config' => ['chart_type' => 'doughnut'],
            ];
        } elseif ($user->hasRole('Student')) {
            $widgetConfigs[] = [
                'widget_slug' => 'my-timetable',
                'position' => ['x' => 0, 'y' => 1, 'w' => 6, 'h' => 2],
                'config' => null,
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'my-attendance',
                'position' => ['x' => 6, 'y' => 1, 'w' => 6, 'h' => 2],
                'config' => ['chart_type' => 'line'],
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'my-grades',
                'position' => ['x' => 0, 'y' => 3, 'w' => 6, 'h' => 3],
                'config' => null,
            ];
            
            $widgetConfigs[] = [
                'widget_slug' => 'upcoming-assignments',
                'position' => ['x' => 6, 'y' => 3, 'w' => 6, 'h' => 3],
                'config' => null,
            ];
        }

        return $widgetConfigs;
    }

    /**
     * Get data for a specific widget.
     *
     * @param DashboardWidgetInstance $instance
     * @return array
     */
    public function getWidgetData(DashboardWidgetInstance $instance)
    {
        $widget = $instance->widget;
        $config = $instance->getEffectiveConfig();
        $widgetType = $widget->widget_type;
        $dataSource = $widget->data_source;

        // Initialize default response structure
        $response = [
            'success' => true,
            'title' => $widget->name,
            'type' => $widgetType,
            'config' => $config,
        ];

        // Get data based on widget type
        switch ($widgetType) {
            case 'count':
                $response['data'] = $this->getCountWidgetData($instance);
                break;
            case 'chart':
                $response['data'] = $this->getChartWidgetData($instance);
                break;
            case 'table':
                $response['data'] = $this->getTableWidgetData($instance);
                break;
            case 'metric':
                $response['data'] = $this->getMetricWidgetData($instance);
                break;
            case 'list':
                $response['data'] = $this->getListWidgetData($instance);
                break;
            case 'calendar':
                $response['data'] = $this->getCalendarWidgetData($instance);
                break;
            case 'custom':
                // For custom widgets, directly use the query if available
                if ($widget->query) {
                    $response['data'] = $this->executeCustomQuery($widget->query, $config);
                } else {
                    $response['data'] = $this->getDataFromSlug($widget->slug, $config);
                }
                break;
            default:
                $response['success'] = false;
                $response['error'] = 'Unsupported widget type';
        }

        return $response;
    }

    /**
     * Get data for count type widgets.
     *
     * @param DashboardWidgetInstance $instance
     * @return array
     */
    protected function getCountWidgetData(DashboardWidgetInstance $instance)
    {
        $widget = $instance->widget;
        $config = $instance->getEffectiveConfig();
        $dataSource = $widget->data_source;
        $entity = $dataSource['entity'] ?? null;
        $timeRange = $config['time_range'] ?? 'all';
        
        $query = null;
        $count = 0;
        $previousCount = 0;
        $change = 0;
        $changePercentage = 0;
        
        // Set date ranges
        $endDate = Carbon::now();
        $startDate = $this->getStartDateForRange($timeRange);
        $previousStartDate = $this->getStartDateForRange($timeRange, true);
        $previousEndDate = $startDate->copy()->subDay();
        
        // Build query based on entity
        switch ($entity) {
            case 'students':
                $query = Student::query();
                break;
            case 'users':
                $query = User::query();
                break;
            case 'departments':
                $query = Department::query();
                break;
            case 'academic_years':
                $query = AcademicYear::query();
                break;
            case 'academic_terms':
                $query = AcademicTerm::query();
                break;
            default:
                // Try to get data from analytics
                if (isset($dataSource['metric_slug'])) {
                    $metricData = $this->analyticsService->getComparisonData(
                        $dataSource['metric_slug'],
                        $startDate,
                        $endDate,
                        $previousStartDate,
                        $previousEndDate
                    );
                    
                    return [
                        'count' => $metricData['current_value'],
                        'previous_count' => $metricData['previous_value'],
                        'change' => $metricData['change'],
                        'change_percentage' => $metricData['change_percentage'],
                        'time_range' => $timeRange,
                    ];
                }
                return [
                    'count' => 0,
                    'previous_count' => 0,
                    'change' => 0,
                    'change_percentage' => 0,
                    'time_range' => $timeRange,
                ];
        }
        
        // Apply common filters
        if (isset($dataSource['filters']) && is_array($dataSource['filters'])) {
            foreach ($dataSource['filters'] as $field => $value) {
                $query->where($field, $value);
            }
        }
        
        // Apply time range filter if the entity has created_at
        if ($timeRange !== 'all' && in_array($entity, ['students', 'users'])) {
            $count = (clone $query)->whereBetween('created_at', [$startDate, $endDate])->count();
            $previousCount = (clone $query)->whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
        } else {
            $count = $query->count();
            $previousCount = $count; // For entities without time series, previous is same as current
        }
        
        // Calculate change
        if ($previousCount > 0) {
            $change = $count - $previousCount;
            $changePercentage = ($change / $previousCount) * 100;
        } else if ($count > 0) {
            $change = $count;
            $changePercentage = 100;
        }
        
        return [
            'count' => $count,
            'previous_count' => $previousCount,
            'change' => $change,
            'change_percentage' => $changePercentage,
            'time_range' => $timeRange,
        ];
    }

    /**
     * Get data for chart type widgets.
     *
     * @param DashboardWidgetInstance $instance
     * @return array
     */
    protected function getChartWidgetData(DashboardWidgetInstance $instance)
    {
        $widget = $instance->widget;
        $config = $instance->getEffectiveConfig();
        $dataSource = $widget->data_source;
        
        $chartType = $config['chart_type'] ?? 'line';
        $timeRange = $config['time_range'] ?? 'month';
        $interval = $config['interval'] ?? 'day';
        
        // Set date ranges
        $endDate = Carbon::now();
        $startDate = $this->getStartDateForRange($timeRange);
        
        // If using analytics metrics
        if (isset($dataSource['metric_slug'])) {
            $filters = $dataSource['filters'] ?? [];
            
            if (isset($dataSource['dimension_slug'])) {
                // Data grouped by dimension (for pie/doughnut/bar charts)
                $dimensionData = $this->analyticsService->getDataByDimension(
                    $dataSource['metric_slug'],
                    $dataSource['dimension_slug'],
                    array_merge($filters, [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ])
                );
                
                $labels = [];
                $data = [];
                
                foreach ($dimensionData as $item) {
                    $labels[] = $item->dimension_value;
                    $data[] = $item->value;
                }
                
                return [
                    'chart_type' => $chartType,
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => $dataSource['label'] ?? $dataSource['metric_slug'],
                            'data' => $data,
                        ]
                    ],
                    'time_range' => $timeRange,
                ];
            } else {
                // Time series data (for line/bar charts)
                $timeSeriesData = $this->analyticsService->getTimeSeriesData(
                    $dataSource['metric_slug'],
                    $startDate,
                    $endDate,
                    $interval,
                    $filters
                );
                
                $labels = [];
                $data = [];
                
                foreach ($timeSeriesData as $item) {
                    $labels[] = $item->time_period;
                    $data[] = $item->value;
                }
                
                return [
                    'chart_type' => $chartType,
                    'labels' => $labels,
                    'datasets' => [
                        [
                            'label' => $dataSource['label'] ?? $dataSource['metric_slug'],
                            'data' => $data,
                        ]
                    ],
                    'time_range' => $timeRange,
                ];
            }
        }
        
        // If using custom query
        if (isset($dataSource['query'])) {
            return $this->executeCustomQuery($dataSource['query'], $config);
        }
        
        // Default chart data
        return [
            'chart_type' => $chartType,
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'No Data',
                    'data' => [],
                ]
            ],
            'time_range' => $timeRange,
        ];
    }

    /**
     * Get data for table type widgets.
     *
     * @param DashboardWidgetInstance $instance
     * @return array
     */
    protected function getTableWidgetData(DashboardWidgetInstance $instance)
    {
        $widget = $instance->widget;
        $config = $instance->getEffectiveConfig();
        $dataSource = $widget->data_source;
        
        $limit = $config['limit'] ?? 10;
        $page = $config['page'] ?? 1;
        $columns = $config['columns'] ?? [];
        
        // If using custom query
        if (isset($dataSource['query'])) {
            $data = $this->executeCustomQuery($dataSource['query'], $config);
            
            if (!empty($data) && is_array($data)) {
                return [
                    'columns' => $columns ?: array_keys($data[0] ?? []),
                    'rows' => $data,
                    'total' => count($data),
                    'page' => $page,
                    'limit' => $limit,
                ];
            }
        }
        
        // Based on entity type
        $entity = $dataSource['entity'] ?? null;
        $query = null;
        
        switch ($entity) {
            case 'students':
                $query = Student::query();
                break;
            case 'users':
                $query = User::query();
                break;
            case 'departments':
                $query = Department::query();
                break;
            default:
                return [
                    'columns' => $columns,
                    'rows' => [],
                    'total' => 0,
                    'page' => $page,
                    'limit' => $limit,
                ];
        }
        
        // Apply filters
        if (isset($dataSource['filters']) && is_array($dataSource['filters'])) {
            foreach ($dataSource['filters'] as $field => $value) {
                $query->where($field, $value);
            }
        }
        
        // Apply sorting
        if (isset($config['sort_field'])) {
            $direction = $config['sort_direction'] ?? 'asc';
            $query->orderBy($config['sort_field'], $direction);
        }
        
        $total = $query->count();
        $rows = $query->skip(($page - 1) * $limit)->take($limit)->get();
        
        // If columns are not specified, use all columns from the result
        if (empty($columns) && $rows->isNotEmpty()) {
            $columns = array_keys($rows->first()->toArray());
        }
        
        return [
            'columns' => $columns,
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    /**
     * Get data for metric type widgets.
     *
     * @param DashboardWidgetInstance $instance
     * @return array
     */
    protected function getMetricWidgetData(DashboardWidgetInstance $instance)
    {
        $widget = $instance->widget;
        $config = $instance->getEffectiveConfig();
        $dataSource = $widget->data_source;
        
        // If using analytics metrics
        if (isset($dataSource['metric_slug'])) {
            $timeRange = $config['time_range'] ?? 'month';
            $endDate = Carbon::now();
            $startDate = $this->getStartDateForRange($timeRange);
            $previousStartDate = $this->getStartDateForRange($timeRange, true);
            $previousEndDate = $startDate->copy()->subDay();
            
            $filters = $dataSource['filters'] ?? [];
            
            $metricData = $this->analyticsService->getComparisonData(
                $dataSource['metric_slug'],
                $startDate,
                $endDate,
                $previousStartDate,
                $previousEndDate,
                $filters
            );
            
            return [
                'value' => $metricData['current_value'],
                'previous_value' => $metricData['previous_value'],
                'change' => $metricData['change'],
                'change_percentage' => $metricData['change_percentage'],
                'time_range' => $timeRange,
                'data_type' => $metricData['data_type'] ?? 'numeric',
                'display_options' => $metricData['display_options'] ?? null,
            ];
        }
        
        // If using custom query
        if (isset($dataSource['query'])) {
            return $this->executeCustomQuery($dataSource['query'], $config);
        }
        
        // Default metric data
        return [
            'value' => 0,
            'previous_value' => 0,
            'change' => 0,
            'change_percentage' => 0,
            'time_range' => $config['time_range'] ?? 'month',
            'data_type' => 'numeric',
        ];
    }

    /**
     * Get data for list type widgets.
     *
     * @param DashboardWidgetInstance $instance
     * @return array
     */
    protected function getListWidgetData(DashboardWidgetInstance $instance)
    {
        $widget = $instance->widget;
        $config = $instance->getEffectiveConfig();
        $dataSource = $widget->data_source;
        
        $limit = $config['limit'] ?? 10;
        
        // If using custom query
        if (isset($dataSource['query'])) {
            return $this->executeCustomQuery($dataSource['query'], $config);
        }
        
        $entity = $dataSource['entity'] ?? null;
        $query = null;
        
        switch ($entity) {
            case 'students':
                $query = Student::query();
                break;
            case 'users':
                $query = User::query();
                break;
            default:
                return [
                    'items' => [],
                    'total' => 0,
                    'has_more' => false,
                ];
        }
        
        // Apply filters
        if (isset($dataSource['filters']) && is_array($dataSource['filters'])) {
            foreach ($dataSource['filters'] as $field => $value) {
                $query->where($field, $value);
            }
        }
        
        // Apply sorting
        if (isset($config['sort_field'])) {
            $direction = $config['sort_direction'] ?? 'asc';
            $query->orderBy($config['sort_field'], $direction);
        } else {
            $query->latest();
        }
        
        $total = $query->count();
        $items = $query->take($limit)->get();
        
        return [
            'items' => $items,
            'total' => $total,
            'has_more' => $total > $limit,
        ];
    }

    /**
     * Get data for calendar type widgets.
     *
     * @param DashboardWidgetInstance $instance
     * @return array
     */
    protected function getCalendarWidgetData(DashboardWidgetInstance $instance)
    {
        $widget = $instance->widget;
        $config = $instance->getEffectiveConfig();
        $dataSource = $widget->data_source;
        
        $year = $config['year'] ?? Carbon::now()->year;
        $month = $config['month'] ?? Carbon::now()->month;
        
        // If using custom query
        if (isset($dataSource['query'])) {
            return $this->executeCustomQuery($dataSource['query'], $config);
        }
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        $events = [];
        
        // Default calendar data
        return [
            'year' => $year,
            'month' => $month,
            'events' => $events,
        ];
    }

    /**
     * Execute a custom query for widget data.
     *
     * @param string $query
     * @param array $config
     * @return array
     */
    protected function executeCustomQuery($query, array $config = [])
    {
        try {
            $timeRange = $config['time_range'] ?? 'month';
            
            // Replace placeholders in the query
            $query = str_replace(
                ['{{start_date}}', '{{end_date}}'],
                [$this->getStartDateForRange($timeRange)->format('Y-m-d'), Carbon::now()->format('Y-m-d')],
                $query
            );
            
            // Execute the query
            $results = DB::select($query);
            
            // Convert to array
            return json_decode(json_encode($results), true);
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get data for a specific widget slug.
     *
     * @param string $slug
     * @param array $config
     * @return array
     */
    protected function getDataFromSlug($slug, array $config = [])
    {
        // Implement specific logic for known widget slugs
        switch ($slug) {
            case 'quick-stats':
                return $this->getQuickStatsData($config);
            case 'system-health':
                return $this->getSystemHealthData($config);
            case 'user-activity':
                return $this->getUserActivityData($config);
            case 'student-enrollment-trends':
                return $this->getStudentEnrollmentTrendsData($config);
            case 'recent-activities':
                return $this->getRecentActivitiesData($config);
            default:
                return [
                    'message' => 'No specific data handler for this widget',
                ];
        }
    }

    /**
     * Get start date for a time range.
     *
     * @param string $range
     * @param bool $previous
     * @return Carbon
     */
    protected function getStartDateForRange($range, $previous = false)
    {
        $now = Carbon::now();
        
        switch ($range) {
            case 'day':
                return $previous ? $now->copy()->subDays(2) : $now->copy()->subDay();
            case 'week':
                return $previous ? $now->copy()->subWeeks(2) : $now->copy()->subWeek();
            case 'month':
                return $previous ? $now->copy()->subMonths(2) : $now->copy()->subMonth();
            case 'quarter':
                return $previous ? $now->copy()->subMonths(6) : $now->copy()->subMonths(3);
            case 'year':
                return $previous ? $now->copy()->subYears(2) : $now->copy()->subYear();
            case 'all':
            default:
                return $previous ? Carbon::createFromTimestamp(0) : Carbon::createFromTimestamp(0);
        }
    }

    /**
     * Get quick stats data.
     *
     * @param array $config
     * @return array
     */
    protected function getQuickStatsData(array $config = [])
    {
        $timeRange = $config['time_range'] ?? 'month';
        $endDate = Carbon::now();
        $startDate = $this->getStartDateForRange($timeRange);
        
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', true)->count();
        $newStudents = Student::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalUsers = User::count();
        
        return [
            'stats' => [
                [
                    'title' => 'Total Students',
                    'value' => $totalStudents,
                    'icon' => 'users',
                    'color' => 'blue',
                ],
                [
                    'title' => 'Active Students',
                    'value' => $activeStudents,
                    'icon' => 'user-check',
                    'color' => 'green',
                ],
                [
                    'title' => 'New Students',
                    'value' => $newStudents,
                    'icon' => 'user-plus',
                    'color' => 'purple',
                ],
                [
                    'title' => 'Total Users',
                    'value' => $totalUsers,
                    'icon' => 'users',
                    'color' => 'orange',
                ],
            ],
            'time_range' => $timeRange,
        ];
    }

    /**
     * Get system health data.
     *
     * @param array $config
     * @return array
     */
    protected function getSystemHealthData(array $config = [])
    {
        return [
            'system_info' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'environment' => app()->environment(),
                'database' => config('database.default'),
            ],
            'health_checks' => [
                [
                    'name' => 'Database Connection',
                    'status' => 'healthy',
                    'details' => 'Connection established successfully',
                ],
                [
                    'name' => 'Storage',
                    'status' => 'healthy',
                    'details' => 'Storage is writable',
                ],
                [
                    'name' => 'Cache',
                    'status' => 'healthy',
                    'details' => 'Cache is functioning properly',
                ],
                [
                    'name' => 'Queue',
                    'status' => 'healthy',
                    'details' => 'Queue system is operational',
                ],
            ],
        ];
    }

    /**
     * Get user activity data.
     *
     * @param array $config
     * @return array
     */
    protected function getUserActivityData(array $config = [])
    {
        $limit = $config['limit'] ?? 10;
        
        // Placeholder for actual user activity tracking
        $activities = [
            [
                'user' => 'Admin User',
                'action' => 'Created a new student record',
                'time' => Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'),
                'ip' => '192.168.1.1',
            ],
            [
                'user' => 'Teacher User',
                'action' => 'Updated attendance records',
                'time' => Carbon::now()->subMinutes(15)->format('Y-m-d H:i:s'),
                'ip' => '192.168.1.2',
            ],
            [
                'user' => 'Admin User',
                'action' => 'Generated student report',
                'time' => Carbon::now()->subMinutes(30)->format('Y-m-d H:i:s'),
                'ip' => '192.168.1.1',
            ],
        ];
        
        return [
            'activities' => $activities,
            'total' => count($activities),
            'limit' => $limit,
        ];
    }

    /**
     * Get student enrollment trends data.
     *
     * @param array $config
     * @return array
     */
    protected function getStudentEnrollmentTrendsData(array $config = [])
    {
        $chartType = $config['chart_type'] ?? 'line';
        $timeRange = $config['time_range'] ?? 'year';
        
        // Placeholder for actual enrollment data
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $datasets = [
            [
                'label' => 'Student Enrollments',
                'data' => [65, 59, 80, 81, 56, 55, 40, 90, 95, 87, 75, 80],
                'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                'borderColor' => 'rgba(54, 162, 235, 1)',
            ],
        ];
        
        return [
            'chart_type' => $chartType,
            'labels' => $labels,
            'datasets' => $datasets,
            'time_range' => $timeRange,
        ];
    }

    /**
     * Get recent activities data.
     *
     * @param array $config
     * @return array
     */
    protected function getRecentActivitiesData(array $config = [])
    {
        $limit = $config['limit'] ?? 10;
        
        // Placeholder for actual activity data
        $activities = [
            [
                'title' => 'New student registered',
                'description' => 'John Doe has registered as a new student',
                'time' => Carbon::now()->subHours(2)->format('Y-m-d H:i:s'),
                'type' => 'student',
            ],
            [
                'title' => 'Attendance recorded',
                'description' => 'Teacher Mark Smith recorded attendance for Class 10A',
                'time' => Carbon::now()->subHours(3)->format('Y-m-d H:i:s'),
                'type' => 'attendance',
            ],
            [
                'title' => 'Fee payment received',
                'description' => 'Jane Doe paid $500 for tuition fees',
                'time' => Carbon::now()->subHours(5)->format('Y-m-d H:i:s'),
                'type' => 'payment',
            ],
        ];
        
        return [
            'activities' => $activities,
            'total' => count($activities),
            'limit' => $limit,
        ];
    }
} 