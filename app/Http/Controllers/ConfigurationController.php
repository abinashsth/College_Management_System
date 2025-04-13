<?php

namespace App\Http\Controllers;

use App\Models\CollegeSettings;
use App\Models\AcademicYear;
use App\Models\AcademicStructure;
use App\Models\SystemSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConfigurationController extends Controller
{
    /**
     * Display the configuration dashboard.
     */
    public function index()
    {
        $collegeSettings = CollegeSettings::first();
        $academicYears = AcademicYear::count();
        $academicStructures = AcademicStructure::count();
        $systemSettings = SystemSettings::count();
        
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();
        $currentSession = null;
        
        if ($currentAcademicYear) {
            $currentSession = $currentAcademicYear->sessions()->where('is_current', true)->first();
        }

        return view('settings.dashboard', compact(
            'collegeSettings',
            'academicYears',
            'academicStructures',
            'systemSettings',
            'currentAcademicYear',
            'currentSession'
        ));
    }

    /**
     * Export configuration settings.
     */
    public function export()
    {
        $collegeSettings = CollegeSettings::first();
        $academicYears = AcademicYear::with('sessions')->get();
        $academicStructures = AcademicStructure::all();
        $systemSettings = SystemSettings::where('is_public', true)->get();

        $exportData = [
            'college_settings' => $collegeSettings ? $collegeSettings->toArray() : null,
            'academic_years' => $academicYears->toArray(),
            'academic_structures' => $academicStructures->toArray(),
            'system_settings' => $systemSettings->toArray(),
            'export_date' => now()->toIso8601String(),
            'version' => '1.0',
        ];

        $filename = 'college_configuration_' . now()->format('Y-m-d_His') . '.json';
        Storage::put('exports/' . $filename, json_encode($exportData, JSON_PRETTY_PRINT));

        return response()->download(storage_path('app/exports/' . $filename))
            ->deleteFileAfterSend();
    }

    /**
     * Show import form.
     */
    public function showImport()
    {
        return view('settings.import');
    }

    /**
     * Import configuration settings.
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:json|max:2048',
            'import_options' => 'required|array',
        ]);

        $file = $request->file('import_file');
        $data = json_decode(file_get_contents($file->getPathname()), true);

        // Validate import data format
        if (!isset($data['version']) || !isset($data['export_date'])) {
            return back()->with('error', 'Invalid import file format');
        }

        DB::beginTransaction();
        
        try {
            // Import college settings
            if (in_array('college_settings', $request->import_options) && isset($data['college_settings'])) {
                $this->importCollegeSettings($data['college_settings']);
            }

            // Import academic structures
            if (in_array('academic_structures', $request->import_options) && isset($data['academic_structures'])) {
                $this->importAcademicStructures($data['academic_structures']);
            }

            // Import academic years and sessions
            if (in_array('academic_years', $request->import_options) && isset($data['academic_years'])) {
                $this->importAcademicYears($data['academic_years']);
            }

            // Import system settings
            if (in_array('system_settings', $request->import_options) && isset($data['system_settings'])) {
                $this->importSystemSettings($data['system_settings']);
            }

            DB::commit();
            return redirect()->route('settings.dashboard')
                ->with('success', 'Configuration imported successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Import college settings.
     */
    private function importCollegeSettings($data)
    {
        $collegeSettings = CollegeSettings::first() ?? new CollegeSettings();
        unset($data['id']);
        unset($data['created_at']);
        unset($data['updated_at']);
        $collegeSettings->fill($data);
        $collegeSettings->save();
    }

    /**
     * Import academic structures.
     */
    private function importAcademicStructures($structures)
    {
        // First pass: Create all structures without parent relationships
        $idMap = [];
        
        foreach ($structures as $structure) {
            $oldId = $structure['id'];
            unset($structure['id']);
            unset($structure['created_at']);
            unset($structure['updated_at']);
            $structure['parent_id'] = null;
            
            $newStructure = AcademicStructure::updateOrCreate(
                ['code' => $structure['code']],
                $structure
            );
            
            $idMap[$oldId] = $newStructure->id;
        }
        
        // Second pass: Update parent relationships
        foreach ($structures as $structure) {
            if (!empty($structure['parent_id']) && isset($idMap[$structure['parent_id']])) {
                AcademicStructure::where('code', $structure['code'])
                    ->update(['parent_id' => $idMap[$structure['parent_id']]]);
            }
        }
    }

    /**
     * Import academic years and sessions.
     */
    private function importAcademicYears($years)
    {
        foreach ($years as $year) {
            $sessions = $year['sessions'] ?? [];
            unset($year['id']);
            unset($year['created_at']);
            unset($year['updated_at']);
            unset($year['sessions']);
            
            $academicYear = AcademicYear::updateOrCreate(
                ['name' => $year['name']],
                $year
            );
            
            foreach ($sessions as $session) {
                unset($session['id']);
                unset($session['academic_year_id']);
                unset($session['created_at']);
                unset($session['updated_at']);
                
                $academicYear->sessions()->updateOrCreate(
                    ['name' => $session['name']],
                    $session
                );
            }
        }
    }

    /**
     * Import system settings.
     */
    private function importSystemSettings($settings)
    {
        foreach ($settings as $setting) {
            unset($setting['id']);
            unset($setting['created_at']);
            unset($setting['updated_at']);
            
            SystemSettings::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
