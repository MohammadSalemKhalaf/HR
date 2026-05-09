<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

   public function rules(): array
{
    $companyId = $this->route('company');

    return [
        'name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('companies', 'name')->ignore($companyId),
        ],

        'industry' => ['required', 'string', 'max:255'],
        'address'  => ['nullable', 'string', 'max:255'],

        'website' => [
            'nullable',
            'url',
            Rule::unique('companies', 'website')->ignore($companyId),
        ],

            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
    ];
}

    public function messages(): array
    {
        return [
            'name.required' => 'Company name is required',
            'name.unique'   => 'Company name already exists',

            'website.url'   => 'Website must be a valid URL',
            'website.unique'=> 'Website already exists',

            'ownerId.exists'=> 'Selected owner does not exist',
            
        ];
    }
}