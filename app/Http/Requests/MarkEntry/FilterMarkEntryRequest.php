declare(strict_types=1);

namespace App\Http\Requests\MarkEntry;

use Illuminate\Foundation\Http\FormRequest;

class FilterMarkEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'faculty_id' => ['required', 'exists:faculties,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_id' => ['required', 'exists:exams,id'],
        ];
    }
} 