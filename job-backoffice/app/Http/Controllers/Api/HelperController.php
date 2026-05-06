<?php

namespace App\Http\Controllers\Api;

use App\Models\Resume;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HelperController extends BaseApiController
{
    public function me(Request $request): JsonResponse
    {
        return app(AuthController::class)->me($request, app(\App\Services\ApiTokenService::class));
    }

    public function resumes(Request $request): JsonResponse
    {
        $query = Resume::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('userId', $request->string('user_id'));
        }

        return $this->success('Resumes retrieved successfully.', $query->get());
    }

    public function storeResume(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'filename' => ['nullable', 'string', 'max:255'],
            'file_url' => ['nullable', 'string', 'max:255'],
            'contact_details' => ['nullable', 'string'],
            'education' => ['nullable', 'string'],
            'experience' => ['nullable', 'string'],
            'skills' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $resume = Resume::create([
            'userId' => $request->string('user_id'),
            'filename' => $request->input('filename', 'resume-'.$request->string('user_id').'.pdf'),
            'fileUrl' => $request->input('file_url', '/storage/resumes/resume-'.$request->string('user_id').'.pdf'),
            'contactDetails' => $request->input('contact_details', 'N/A'),
            'education' => $request->input('education', 'N/A'),
            'experience' => $request->input('experience', 'N/A'),
            'skills' => $request->input('skills', 'N/A'),
            'summary' => $request->input('summary', 'N/A'),
        ]);

        return $this->success('Resume created successfully.', $resume->load('user'), 201);
    }
}
