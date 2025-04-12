<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('permission:view activity logs');
    }

    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->inModule($request->module);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->withAction($request->action);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $activityLogs = $query->paginate(15)->withQueryString();
        
        // Get all available modules and actions for filter dropdowns
        $modules = ActivityLog::select('module')->distinct()->pluck('module');
        $actions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('activity_logs.index', compact('activityLogs', 'modules', 'actions'));
    }

    /**
     * Display the specified activity log.
     */
    public function show(ActivityLog $activityLog)
    {
        return view('activity_logs.show', compact('activityLog'));
    }

    /**
     * Clear all activity logs.
     */
    public function clear()
    {
        ActivityLog::truncate();
        return redirect()->route('activity-logs.index')->with('success', 'All activity logs have been cleared.');
    }
} 