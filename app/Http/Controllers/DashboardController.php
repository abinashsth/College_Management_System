<?php

namespace App\Http\Controllers;

use App\Models\Dashboard\DashboardWidget;
use App\Models\Dashboard\UserDashboard;
use App\Models\Dashboard\DashboardWidgetInstance;
use App\Services\AnalyticsService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $analyticsService;

    public function __construct(DashboardService $dashboardService, AnalyticsService $analyticsService)
    {
        $this->dashboardService = $dashboardService;
        $this->analyticsService = $analyticsService;
        $this->middleware('auth');
    }

    /**
     * Display the user's default dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get the user's default dashboard or create one if none exists
        $dashboard = UserDashboard::forUser($user->id)->defaultDashboards()->first();
        
        if (!$dashboard) {
            $dashboard = $this->dashboardService->createDefaultDashboard($user);
        }
        
        return $this->showDashboard($dashboard->id);
    }

    /**
     * Show a specific dashboard.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDashboard($id)
    {
        $user = Auth::user();
        $dashboard = UserDashboard::visibleToUser($user)->findOrFail($id);
        
        $widgetInstances = $dashboard->widgetInstances()
            ->with('widget')
            ->get()
            ->map(function ($instance) {
                $widget = $instance->widget;
                return [
                    'instance_id' => $instance->id,
                    'widget_id' => $widget->id,
                    'name' => $widget->name,
                    'type' => $widget->widget_type,
                    'position' => [
                        'x' => $instance->position_x,
                        'y' => $instance->position_y,
                        'w' => $instance->width,
                        'h' => $instance->height,
                    ],
                    'config' => $instance->getEffectiveConfig(),
                    'refresh_interval' => $widget->refresh_interval,
                ];
            });
        
        $availableWidgets = DashboardWidget::active()
            ->visibleToUser($user)
            ->orderBy('name')
            ->get();
        
        $userDashboards = UserDashboard::visibleToUser($user)
            ->orderBy('name')
            ->get()
            ->map(function ($d) use ($id) {
                return [
                    'id' => $d->id,
                    'name' => $d->name,
                    'is_default' => $d->is_default,
                    'is_current' => $d->id == $id,
                ];
            });
        
        return view('dashboard.show', compact(
            'dashboard', 
            'widgetInstances', 
            'availableWidgets', 
            'userDashboards'
        ));
    }

    /**
     * Show the form for creating a new dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.create');
    }

    /**
     * Store a newly created dashboard in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_shared' => 'boolean',
            'is_default' => 'boolean',
        ]);
        
        $user = Auth::user();
        
        // If this dashboard is set as default, remove default from others
        if ($request->input('is_default', false)) {
            UserDashboard::forUser($user->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
        
        $dashboard = new UserDashboard();
        $dashboard->user_id = $user->id;
        $dashboard->name = $validatedData['name'];
        $dashboard->slug = Str::slug($validatedData['name']) . '-' . Str::random(5);
        $dashboard->description = $validatedData['description'] ?? null;
        $dashboard->is_shared = $validatedData['is_shared'] ?? false;
        $dashboard->is_default = $validatedData['is_default'] ?? false;
        $dashboard->layout = [];
        $dashboard->save();
        
        return redirect()->route('dashboard.show', $dashboard->id)
            ->with('success', 'Dashboard created successfully.');
    }

    /**
     * Show the form for editing the specified dashboard.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $dashboard = UserDashboard::visibleToUser($user)->findOrFail($id);
        
        // Only the owner or an admin can edit a dashboard
        if ($dashboard->user_id != $user->id && !$user->hasRole(['Super Admin', 'Admin'])) {
            return redirect()->route('dashboard.show', $id)
                ->with('error', 'You do not have permission to edit this dashboard.');
        }
        
        return view('dashboard.edit', compact('dashboard'));
    }

    /**
     * Update the specified dashboard in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_shared' => 'boolean',
            'is_default' => 'boolean',
        ]);
        
        $user = Auth::user();
        $dashboard = UserDashboard::visibleToUser($user)->findOrFail($id);
        
        // Only the owner or an admin can update a dashboard
        if ($dashboard->user_id != $user->id && !$user->hasRole(['Super Admin', 'Admin'])) {
            return redirect()->route('dashboard.show', $id)
                ->with('error', 'You do not have permission to update this dashboard.');
        }
        
        // If this dashboard is set as default, remove default from others
        if ($request->input('is_default', false) && !$dashboard->is_default) {
            UserDashboard::forUser($dashboard->user_id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
        
        $dashboard->name = $validatedData['name'];
        $dashboard->description = $validatedData['description'] ?? null;
        $dashboard->is_shared = $validatedData['is_shared'] ?? false;
        $dashboard->is_default = $validatedData['is_default'] ?? false;
        $dashboard->save();
        
        return redirect()->route('dashboard.show', $id)
            ->with('success', 'Dashboard updated successfully.');
    }

    /**
     * Remove the specified dashboard from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $dashboard = UserDashboard::visibleToUser($user)->findOrFail($id);
        
        // Only the owner or an admin can delete a dashboard
        if ($dashboard->user_id != $user->id && !$user->hasRole(['Super Admin', 'Admin'])) {
            return redirect()->route('dashboard.show', $id)
                ->with('error', 'You do not have permission to delete this dashboard.');
        }
        
        // Delete all widget instances first
        $dashboard->widgetInstances()->delete();
        
        // Delete the dashboard
        $dashboard->delete();
        
        return redirect()->route('dashboard.index')
            ->with('success', 'Dashboard deleted successfully.');
    }

    /**
     * Add a widget to a dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $dashboardId
     * @return \Illuminate\Http\Response
     */
    public function addWidget(Request $request, $dashboardId)
    {
        $validatedData = $request->validate([
            'widget_id' => 'required|exists:dashboard_widgets,id',
            'position_x' => 'required|integer|min:0',
            'position_y' => 'required|integer|min:0',
            'width' => 'required|integer|min:1|max:12',
            'height' => 'required|integer|min:1|max:12',
            'config' => 'nullable|json',
        ]);
        
        $user = Auth::user();
        $dashboard = UserDashboard::visibleToUser($user)->findOrFail($dashboardId);
        
        // Only the owner or an admin can add widgets to a dashboard
        if ($dashboard->user_id != $user->id && !$user->hasRole(['Super Admin', 'Admin'])) {
            return response()->json(['error' => 'You do not have permission to modify this dashboard.'], 403);
        }
        
        $widget = DashboardWidget::active()->visibleToUser($user)->findOrFail($validatedData['widget_id']);
        
        $instance = new DashboardWidgetInstance();
        $instance->dashboard_id = $dashboard->id;
        $instance->widget_id = $widget->id;
        $instance->position_x = $validatedData['position_x'];
        $instance->position_y = $validatedData['position_y'];
        $instance->width = $validatedData['width'];
        $instance->height = $validatedData['height'];
        $instance->instance_config = json_decode($validatedData['config'] ?? '{}');
        $instance->save();
        
        return response()->json([
            'success' => true,
            'instance_id' => $instance->id,
            'message' => 'Widget added successfully.'
        ]);
    }

    /**
     * Update a widget instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $instanceId
     * @return \Illuminate\Http\Response
     */
    public function updateWidgetInstance(Request $request, $instanceId)
    {
        $validatedData = $request->validate([
            'position_x' => 'sometimes|required|integer|min:0',
            'position_y' => 'sometimes|required|integer|min:0',
            'width' => 'sometimes|required|integer|min:1|max:12',
            'height' => 'sometimes|required|integer|min:1|max:12',
            'config' => 'nullable|json',
        ]);
        
        $user = Auth::user();
        $instance = DashboardWidgetInstance::with('dashboard')
            ->findOrFail($instanceId);
        
        $dashboard = $instance->dashboard;
        
        // Only the dashboard owner or an admin can update widgets
        if ($dashboard->user_id != $user->id && !$user->hasRole(['Super Admin', 'Admin'])) {
            return response()->json(['error' => 'You do not have permission to modify this dashboard.'], 403);
        }
        
        if (isset($validatedData['position_x'])) {
            $instance->position_x = $validatedData['position_x'];
        }
        
        if (isset($validatedData['position_y'])) {
            $instance->position_y = $validatedData['position_y'];
        }
        
        if (isset($validatedData['width'])) {
            $instance->width = $validatedData['width'];
        }
        
        if (isset($validatedData['height'])) {
            $instance->height = $validatedData['height'];
        }
        
        if (isset($validatedData['config'])) {
            $instance->instance_config = json_decode($validatedData['config']);
        }
        
        $instance->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Widget updated successfully.'
        ]);
    }

    /**
     * Remove a widget from a dashboard.
     *
     * @param  int  $instanceId
     * @return \Illuminate\Http\Response
     */
    public function removeWidget($instanceId)
    {
        $user = Auth::user();
        $instance = DashboardWidgetInstance::with('dashboard')
            ->findOrFail($instanceId);
        
        $dashboard = $instance->dashboard;
        
        // Only the dashboard owner or an admin can remove widgets
        if ($dashboard->user_id != $user->id && !$user->hasRole(['Super Admin', 'Admin'])) {
            return response()->json(['error' => 'You do not have permission to modify this dashboard.'], 403);
        }
        
        $instance->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Widget removed successfully.'
        ]);
    }

    /**
     * Get widget data for AJAX updates.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $instanceId
     * @return \Illuminate\Http\Response
     */
    public function getWidgetData(Request $request, $instanceId)
    {
        $user = Auth::user();
        $instance = DashboardWidgetInstance::with(['dashboard', 'widget'])
            ->findOrFail($instanceId);
        
        $dashboard = $instance->dashboard;
        $widget = $instance->widget;
        
        // Check if the user can view this dashboard
        if (!$dashboard->is_shared && $dashboard->user_id != $user->id && !$user->hasRole(['Super Admin', 'Admin'])) {
            return response()->json(['error' => 'You do not have permission to view this dashboard.'], 403);
        }
        
        // Check if the user can view this widget
        if (!$widget->is_system && !$widget->visibleToUser($user)) {
            return response()->json(['error' => 'You do not have permission to view this widget.'], 403);
        }
        
        // Get the data for this widget based on its type and configuration
        $data = $this->dashboardService->getWidgetData($instance);
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
