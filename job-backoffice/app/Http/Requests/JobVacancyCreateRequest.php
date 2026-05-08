<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class JobVacancyCreateRequest  extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User|null $user */
        $user = Auth::user();

        $companyRule = ['required', 'exists:companies,id'];

        if ($user instanceof User && $user->hasRole('company')) {
            // company users will have company resolved server-side; don't require client-supplied id
            $companyRule = ['nullable', 'exists:companies,id'];
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'numeric', 'min:0'],
            'type' => ['required', 'in:full-time,contract,hybrid,remote'],
            'companyId' => $companyRule,
            'categoryId' => ['required', 'exists:job_categories,id'],
            'description' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The job title is required.',
            'location.required' => 'The job location is required.',
            'salary.required' => 'The job salary is required.',
            'description.required' => 'The job description is required.',
        ];
    }
}
