<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CollegeSettings;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CollegeSettingsController extends Controller
{
    /**
     * Display college settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $settings = CollegeSettings::first() ?? new CollegeSettings();
            return view('settings.college', compact('settings'));
        } catch (\Exception $e) {
            Log::error('Error retrieving college settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Could not load college settings: ' . $e->getMessage());
        }
    }

    /**
     * Update college settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $settings = CollegeSettings::first() ?? new CollegeSettings();
            
            $validated = $request->validate([
                'college_name' => 'required|string|max:255',
                'college_code' => 'required|string|max:50',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'country' => 'required|string|max:100',
                'phone' => 'required|string|max:20',
                'email' => 'required|email|max:255',
                'website' => 'nullable|url|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'established_year' => 'nullable|integer|min:1800|max:' . date('Y'),
                'accreditation_info' => 'nullable|string|max:500',
                'academic_year_start' => 'nullable|date',
                'academic_year_end' => 'nullable|date|after_or_equal:academic_year_start',
                'grading_system' => 'nullable|string|max:255',
                'principal_name' => 'nullable|string|max:255',
                'vision_statement' => 'nullable|string',
                'mission_statement' => 'nullable|string',
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                    Storage::disk('public')->delete($settings->logo);
                }
                
                $logoPath = $request->file('logo')->store('logos', 'public');
                $validated['logo'] = $logoPath;
            }

            // Save settings
            $settings->fill($validated);
            $settings->save();
            
            DB::commit();
            return redirect()->route('settings.college')->with('success', 'College settings updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating college settings: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update college settings: ' . $e->getMessage());
        }
    }
    
    /**
     * Reset logo to default.
     *
     * @return \Illuminate\Http\Response
     */
    public function resetLogo()
    {
        try {
            $settings = CollegeSettings::first();
            
            if (!$settings) {
                return redirect()->route('settings.college')->with('error', 'No college settings found.');
            }
            
            // Delete old logo if exists
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            
            $settings->logo = null;
            $settings->save();
            
            return redirect()->route('settings.college')->with('success', 'College logo reset successfully.');
        } catch (\Exception $e) {
            Log::error('Error resetting college logo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reset college logo: ' . $e->getMessage());
        }
    }
    
    /**
     * Export college settings as JSON.
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        try {
            $settings = CollegeSettings::first();
            
            if (!$settings) {
                return redirect()->route('settings.college')->with('error', 'No college settings found to export.');
            }
            
            $filename = 'college_settings_' . date('Y-m-d') . '.json';
            $settings = $settings->toArray();
            
            // Remove sensitive fields if needed
            unset($settings['created_at'], $settings['updated_at']);
            
            return response()->json($settings)
                ->header('Content-Disposition', 'attachment; filename=' . $filename)
                ->header('Content-Type', 'application/json');
                
        } catch (\Exception $e) {
            Log::error('Error exporting college settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export college settings: ' . $e->getMessage());
        }
    }
    
    /**
     * Import college settings from JSON.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'settings_file' => 'required|file|mimes:json|max:2048',
            ]);
            
            $file = $request->file('settings_file');
            $content = file_get_contents($file->getPathname());
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON file');
            }
            
            $settings = CollegeSettings::first() ?? new CollegeSettings();
            
            // Remove fields that shouldn't be imported
            unset($data['id'], $data['created_at'], $data['updated_at']);
            
            // Don't override logo from import
            if (isset($data['logo'])) {
                unset($data['logo']);
            }
            
            $settings->fill($data);
            $settings->save();
            
            DB::commit();
            return redirect()->route('settings.college')->with('success', 'College settings imported successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error importing college settings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to import college settings: ' . $e->getMessage());
        }
    }
}
