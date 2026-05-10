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
        $user = $request->user();
        $query = Resume::with('user')->latest();

        // Always filter by the current authenticated user
        if ($user) {
            $query->where('userId', $user->id);
        }

        return $this->success('Resumes retrieved successfully.', $query->get());
    }

    public function storeResume(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'cv_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'contact_details' => ['nullable', 'string'],
            'education' => ['nullable', 'string'],
            'experience' => ['nullable', 'string'],
            'skills' => ['nullable', 'string'],
            'summary' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        // store uploaded file
        $file = $request->file('cv_file');
        $filename = $request->string('user_id').'-'.now()->timestamp.'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('public/resumes', $filename);
        $fileUrl = '/storage/resumes/'.$filename;

        $resume = Resume::create([
            'userId' => $request->string('user_id'),
            'filename' => $file->getClientOriginalName(),
            'fileUrl' => $fileUrl,
            'contactDetails' => $request->input('contact_details', 'N/A'),
            'education' => $request->input('education', 'N/A'),
            'experience' => $request->input('experience', 'N/A'),
            'skills' => $request->input('skills', 'N/A'),
            'summary' => $request->input('summary', 'N/A'),
        ]);

        return $this->success('Resume created successfully.', $resume->load('user'), 201);
    }
}
