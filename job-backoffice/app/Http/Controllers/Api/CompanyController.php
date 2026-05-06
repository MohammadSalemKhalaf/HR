<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Company::with('owner')->latest();

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
        }

        return $this->success('Companies retrieved successfully.', $query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'industry' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'owner_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $company = Company::create([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'industry' => $request->input('industry'),
            'website' => $request->input('website'),
            'ownerId' => $request->input('owner_id', $request->user()->id),
        ]);

        return $this->success('Company created successfully.', $company->load('owner'), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $company = Company::find($id);

        if (! $company) {
            return $this->notFound('Company not found.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:255'],
            'industry' => ['sometimes', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'owner_id' => ['sometimes', 'nullable', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $company->update(array_filter([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'industry' => $request->input('industry'),
            'website' => $request->input('website'),
            'ownerId' => $request->input('owner_id'),
        ], static fn ($value) => $value !== null));

        return $this->success('Company updated successfully.', $company->fresh('owner'));
    }

    public function destroy(string $id): JsonResponse
    {
        $company = Company::find($id);

        if (! $company) {
            return $this->notFound('Company not found.');
        }

        $company->delete();

        return $this->success('Company archived successfully.');
    }
}
