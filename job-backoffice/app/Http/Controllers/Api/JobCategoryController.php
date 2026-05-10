<?php

namespace App\Http\Controllers\Api;

use App\Models\JobCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class JobCategoryController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = JobCategory::latest();

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
        }

        $categories = $query->paginate(10);

        return $this->success('Job categories retrieved successfully.', $categories);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', Rule::unique('job_categories', 'name')],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $category = JobCategory::create([
            'name' => $request->input('name'),
        ]);

        return $this->success('Job category created successfully.', $category, 201);
    }

    public function show(string $id): JsonResponse
    {
        $category = JobCategory::find($id);

        if (! $category) {
            return $this->notFound('Job category not found.');
        }

        return $this->success('Job category retrieved successfully.', $category);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $category = JobCategory::find($id);

        if (! $category) {
            return $this->notFound('Job category not found.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('job_categories', 'name')->ignore($category->id)],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $category->update(array_filter([
            'name' => $request->input('name'),
        ], static fn ($value) => $value !== null));

        return $this->success('Job category updated successfully.', $category->fresh());
    }

    public function destroy(string $id): JsonResponse
    {
        $category = JobCategory::find($id);

        if (! $category) {
            return $this->notFound('Job category not found.');
        }

        $category->delete();

        return $this->success('Job category archived successfully.');
    }

    public function restore(string $id): JsonResponse
    {
        $category = JobCategory::withTrashed()->find($id);

        if (! $category) {
            return $this->notFound('Job category not found.');
        }

        if (! $category->trashed()) {
            return $this->error('Job category is not archived.', [], 422);
        }

        $category->restore();

        return $this->success('Job category restored successfully.', $category);
    }
}
