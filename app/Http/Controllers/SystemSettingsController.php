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
        $settings = SystemSettings::orderBy('group');
        $groups = SystemSettings::select('group')->distinct()->pluck('group');
        
        // Filter by search if provided
        if (request()->has('search') && request('search') !== null) {
            $search = request('search');
            $settings->where(function($query) use ($search) {
                $query->where('key', 'like', "%{$search}%")
                      ->orWhere('value', 'like', "%{$search}%");
            });
        }
        
        // Filter by group if provided
        if (request()->has('group') && request('group') !== null && request('group') !== '') {
            $settings->where('group', request('group'));
        }
        
        // Filter by visibility if provided
        if (request()->has('is_public') && request('is_public') !== null && request('is_public') !== '') {
            $settings->where('is_public', request('is_public'));
        }
        
        $settings = $settings->paginate(10);
        
        return view('settings.system.index', compact('settings', 'groups'));
        
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
        $groups = SystemSettings::select('group')->distinct()->pluck('group');
        return view('settings.system.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:system_settings,key',
            'group' => 'required|string|max:50',
            'value' => 'required|string',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        // Convert is_public to boolean since checkboxes may come as null
        $validated['is_public'] = $request->has('is_public') ? 1 : 0;

        SystemSettings::create($validated);

        // Clear the cache for this setting
        Cache::forget('setting.' . $validated['key']);

        return redirect()->route('settings.system.index')
            ->with('success', 'Setting created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SystemSettings $systemSetting)
    {
        return view('settings.system.show', ['setting' => $systemSetting]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SystemSettings $systemSetting)
    {
        $groups = SystemSettings::select('group')->distinct()->pluck('group');
        return view('settings.system.edit', [
            'setting' => $systemSetting,
            'groups' => $groups
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemSettings $systemSetting)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:system_settings,key,' . $systemSetting->id,
            'group' => 'required|string|max:50',
            'value' => 'required|string',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        // Convert is_public to boolean since checkboxes may come as null
        $validated['is_public'] = $request->has('is_public') ? 1 : 0;

        // Clear the cache for both the old key and new key if changed
        Cache::forget('setting.' . $systemSetting->key);
        if ($systemSetting->key !== $validated['key']) {
            Cache::forget('setting.' . $validated['key']);
        }

        $systemSetting->update($validated);

        return redirect()->route('settings.system.index')
            ->with('success', 'Setting updated successfully.');
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

    /**
     * Show the form for duplicating the specified resource.
     */
    public function duplicate(SystemSettings $systemSetting)
    {
        $groups = SystemSettings::select('group')->distinct()->pluck('group');
        return view('settings.system.duplicate', [
            'setting' => $systemSetting,
            'groups' => $groups
        ]);
    }
}
