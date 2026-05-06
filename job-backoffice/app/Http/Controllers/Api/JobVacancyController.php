<?php

namespace App\Http\Controllers\Api;

use App\Models\JobCategory;
use App\Models\JobVacancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobVacancyController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success('Job vacancies retrieved successfully.', JobVacancy::with('company')->latest()->get());
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
            'company_id' => ['required', 'exists:companies,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $categoryId = $request->input('category_id');

        if (! $categoryId) {
            $categoryId = JobCategory::firstOrCreate(['name' => 'General'])->id;
        }

        $jobVacancy = JobVacancy::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'location' => $request->input('location'),
            'salary' => $request->input('salary'),
            'type' => $request->input('type', 'full-time'),
            'categoryId' => $categoryId,
            'companyId' => $request->string('company_id'),
        ]);

        return $this->success('Job vacancy created successfully.', $jobVacancy->load('company'), 201);
    }

    public function show(string $id): JsonResponse
    {
        $jobVacancy = JobVacancy::with('company')->find($id);

        if (! $jobVacancy) {
            return $this->notFound('Job vacancy not found.');
        }

        return $this->success('Job vacancy retrieved successfully.', $jobVacancy);
    }
}
