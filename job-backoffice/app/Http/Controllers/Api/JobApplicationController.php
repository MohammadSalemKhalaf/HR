<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Resume;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JobApplicationController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = JobApplication::with(['user', 'resume', 'jobvacancy.company'])->latest();

        if ($request->filled('company_id')) {
            $query->whereHas('jobvacancy', fn ($builder) => $builder->where('companyId', $request->string('company_id')));
        }

        return $this->success('Applications retrieved successfully.', $query->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'job_vacancy_id' => ['required', 'exists:job_vacancies,id'],
            'resume_id' => ['nullable', 'exists:resumes,id'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $resumeId = $request->input('resume_id');

        if (! $resumeId) {
            $resume = Resume::where('userId', $request->string('user_id'))->latest()->first();

            if (! $resume) {
                $resume = Resume::create([
                    'userId' => $request->string('user_id'),
                    'filename' => 'auto-generated-'.$request->string('user_id').'.pdf',
                    'fileUrl' => '/storage/resumes/auto-generated-'.$request->string('user_id').'.pdf',
                    'contactDetails' => 'N/A',
                    'education' => 'N/A',
                    'experience' => 'N/A',
                    'skills' => 'N/A',
                    'summary' => 'Auto-generated placeholder resume for application flow.',
                ]);
            }

            $resumeId = $resume->id;
        }

        $application = JobApplication::create([
            'status' => 'pending',
            'aiGeneratedScore' => 0,
            'aiGeneratedFeedback' => null,
            'userId' => $request->string('user_id'),
            'resumeId' => $resumeId,
            'jobVacancyId' => $request->string('job_vacancy_id'),
        ]);

        return $this->success('Application created successfully.', $application->load(['user', 'resume', 'jobvacancy.company']), 201);
    }

    public function accept(string $id): JsonResponse
    {
        $application = JobApplication::with('jobvacancy')->find($id);

        if (! $application) {
            return $this->notFound('Application not found.');
        }

        DB::transaction(function () use ($application) {
            $application->update(['status' => 'accepted']);
        });

        $employee = Employee::where('user_id', $application->userId)->with(['user', 'company', 'department', 'manager'])->first();

        return $this->success('Application accepted successfully.', [
            'application' => $application->fresh(['user', 'resume', 'jobvacancy.company']),
            'employee_exists' => (bool) $employee,
            'employee' => $employee,
        ]);
    }

    public function reject(string $id): JsonResponse
    {
        $application = JobApplication::find($id);

        if (! $application) {
            return $this->notFound('Application not found.');
        }

        $application->update(['status' => 'rejected']);

        return $this->success('Application rejected successfully.', $application->fresh(['user', 'resume', 'jobvacancy.company']));
    }

    public function uploadCV(Request $request, string $id): JsonResponse
    {
        $application = JobApplication::find($id);

        if (! $application) {
            return $this->notFound('Application not found.');
        }

        $validator = Validator::make($request->all(), [
            'cv_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        // Handle file upload
        $file = $request->file('cv_file');
        $filename = $application->userId . '-' . now()->timestamp . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/resumes', $filename);
        $fileUrl = '/storage/resumes/' . $filename;

        // Update or create resume
        $resume = Resume::where('userId', $application->userId)->first();

        if ($resume) {
            $resume->update([
                'filename' => $file->getClientOriginalName(),
                'fileUrl' => $fileUrl,
            ]);
        } else {
            $resume = Resume::create([
                'userId' => $application->userId,
                'filename' => $file->getClientOriginalName(),
                'fileUrl' => $fileUrl,
                'contactDetails' => 'N/A',
                'education' => 'N/A',
                'experience' => 'N/A',
                'skills' => 'N/A',
                'summary' => 'Uploaded resume file.',
            ]);
        }

        // Update application with resume
        $application->update(['resumeId' => $resume->id]);

        return $this->success('CV uploaded successfully.', $application->fresh(['user', 'resume', 'jobvacancy.company']), 200);
    }
}
