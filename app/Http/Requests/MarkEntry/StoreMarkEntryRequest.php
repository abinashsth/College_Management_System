declare(strict_types=1);

namespace App\Http\Requests\MarkEntry;

use Illuminate\Foundation\Http\FormRequest;

class StoreMarkEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_id' => ['required', 'exists:exams,id'],
            'marks' => ['required', 'array'],
            'marks.*' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'marks.*.required' => 'Mark is required for all students',
            'marks.*.numeric' => 'Mark must be a number',
            'marks.*.min' => 'Mark cannot be less than 0',
            'marks.*.max' => 'Mark cannot be more than 100',
        ];
    }
} 