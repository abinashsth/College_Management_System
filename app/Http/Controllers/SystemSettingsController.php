<?php

namespace App\Http\Controllers;

use App\Models\SystemSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SystemSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = SystemSettings::orderBy('group')->get()->groupBy('group');
        
        // Try a very simple view name
        return view('settings.system_index', compact('settings'));
        
        // Alternative system view
        // return view('settings.system', compact('settings'));
        
        // Original code
        // return view('settings.system.index', compact('settings'));
        
        // Dashboard view for testing
        // return view('settings.dashboard', [
        //    'systemSettings' => $settings->count(),
        //    'academicYears' => 0,
        //    'academicStructures' => 0
        // ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('settings.system.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:system_settings',
            'value' => 'nullable|string',
            'group' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        SystemSettings::create($validated);

        // Clear cache for this setting
        Cache::forget('setting_' . $request->key);

        return redirect()->route('settings.system.index')
            ->with('success', 'System setting created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(SystemSettings $systemSetting)
    {
        return view('settings.system.show', compact('systemSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SystemSettings $systemSetting)
    {
        return view('settings.system.edit', compact('systemSetting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemSettings $systemSetting)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:system_settings,key,' . $systemSetting->id,
            'value' => 'nullable|string',
            'group' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $systemSetting->update($validated);

        // Clear cache for this setting
        Cache::forget('setting_' . $systemSetting->key);
        if ($request->key != $systemSetting->key) {
            Cache::forget('setting_' . $request->key);
        }

        return redirect()->route('settings.system.index')
            ->with('success', 'System setting updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemSettings $systemSetting)
    {
        // Clear cache for this setting
        Cache::forget('setting_' . $systemSetting->key);

        $systemSetting->delete();

        return redirect()->route('settings.system.index')
            ->with('success', 'System setting deleted successfully');
    }

    /**
     * Bulk update settings.
     */
    public function bulkUpdate(Request $request)
    {
        $settings = $request->except('_token', '_method');
        
        foreach ($settings as $key => $value) {
            $setting = SystemSettings::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
                Cache::forget('setting_' . $key);
            }
        }

        return redirect()->route('settings.system.index')
            ->with('success', 'Settings updated successfully');
    }
}
