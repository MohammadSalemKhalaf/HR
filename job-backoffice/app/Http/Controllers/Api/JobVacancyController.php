<?php

namespace App\Http\Controllers\Api;

use App\Models\JobCategory;
use App\Models\JobVacancy;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobVacancyController extends BaseApiController
{
    private function resolveCompanyId(Request $request): string
    {
        $actingUser = $request->user();

        if ($actingUser instanceof User && $actingUser->hasRole('company')) {
            return (string) ($actingUser->company?->id ?? $actingUser->employee?->company_id ?? '');
        }

        return (string) $request->string('company_id');
    }

    public function index(Request $request): JsonResponse
    {
        $query = JobVacancy::with(['company', 'jobcategory'])->latest();
        $companyId = $this->resolveCompanyId($request);

        if ($companyId !== '') {
            $query->where('companyId', $companyId);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('company', function ($companyBuilder) use ($search) {
                        $companyBuilder->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jobcategory', function ($categoryBuilder) use ($search) {
                        $categoryBuilder->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('category')) {
            $query->where('categoryId', $request->input('category'));
        }

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
        }

        return $this->success('Job vacancies retrieved successfully.', $query->paginate(10));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:full-time,contract,hybrid,remote'],
            'category_id' => ['nullable', 'exists:job_categories,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $categoryId = $request->input('category_id');

        if (! $categoryId) {
            $categoryId = JobCategory::firstOrCreate(['name' => 'General'])->id;
        }

        $resolvedCompanyId = $this->resolveCompanyId($request);

        if ($resolvedCompanyId === '') {
            return $this->error('Validation failed.', ['company_id' => ['Company could not be resolved from token.']], 422);
        }

        $jobVacancy = JobVacancy::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'location' => $request->input('location'),
            'salary' => $request->input('salary'),
            'type' => $request->input('type', 'full-time'),
            'categoryId' => $categoryId,
            'companyId' => $resolvedCompanyId,
        ]);

        return $this->success('Job vacancy created successfully.', $jobVacancy->load(['company', 'jobcategory']), 201);
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $jobVacancy = JobVacancy::with('company')->find($id);

        if (! $jobVacancy) {
            return $this->notFound('Job vacancy not found.');
        }

        $companyId = $this->resolveCompanyId($request);

        if ($companyId !== '' && $jobVacancy->companyId !== $companyId) {
            return $this->notFound('Job vacancy not found.');
        }

        return $this->success('Job vacancy retrieved successfully.', $jobVacancy->load(['company', 'jobcategory']));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $jobVacancy = JobVacancy::find($id);

        if (! $jobVacancy) {
            return $this->notFound('Job vacancy not found.');
        }

        $companyId = $this->resolveCompanyId($request);

        if ($companyId !== '' && $jobVacancy->companyId !== $companyId) {
            return $this->notFound('Job vacancy not found.');
        }

        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'location' => ['sometimes', 'string', 'max:255'],
            'salary' => ['sometimes', 'string', 'max:255'],
            'type' => ['nullable', 'in:full-time,contract,hybrid,remote'],
            'category_id' => ['nullable', 'exists:job_categories,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $jobVacancy->update(array_filter([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'location' => $request->input('location'),
            'salary' => $request->input('salary'),
            'type' => $request->input('type'),
            'categoryId' => $request->input('category_id'),
        ], static fn ($value) => $value !== null));

        return $this->success('Job vacancy updated successfully.', $jobVacancy->fresh(['company', 'jobcategory']));
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $jobVacancy = JobVacancy::find($id);

        if (! $jobVacancy) {
            return $this->notFound('Job vacancy not found.');
        }

        $companyId = $this->resolveCompanyId($request);

        if ($companyId !== '' && $jobVacancy->companyId !== $companyId) {
            return $this->notFound('Job vacancy not found.');
        }

        $jobVacancy->delete();

        return $this->success('Job vacancy archived successfully.');
    }

    public function restore(Request $request, string $id): JsonResponse
    {
        $jobVacancy = JobVacancy::withTrashed()->find($id);

        if (! $jobVacancy) {
            return $this->notFound('Job vacancy not found.');
        }

        $companyId = $this->resolveCompanyId($request);

        if ($companyId !== '' && $jobVacancy->companyId !== $companyId) {
            return $this->notFound('Job vacancy not found.');
        }

        if (! $jobVacancy->trashed()) {
            return $this->error('Job vacancy is not archived.', [], 422);
        }

        $jobVacancy->restore();

        return $this->success('Job vacancy restored successfully.', $jobVacancy->fresh(['company', 'jobcategory']));
    }
}
