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
            'resume_id' => ['nullable'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $userId = $request->string('user_id');
        $jobVacancyId = $request->string('job_vacancy_id');
        $providedResumeId = $request->input('resume_id');
        $cvFile = $request->file('cv_file');
    // Convert Stringable objects to plain strings
    $userId = (string) $userId;
    $jobVacancyId = (string) $jobVacancyId;


        // Require at least one: resume_id or cv_file
        if (! $providedResumeId && ! $cvFile) {
            return $this->error('Either resume_id or cv_file must be provided.', null, 422);
        }

        $resumeId = null;

        // PRIORITY: If cv_file provided, create new resume from it (takes precedence)
        if ($cvFile) {
            $filename = $userId.'-'.now()->timestamp.'.'.$cvFile->getClientOriginalExtension();
            $path = $cvFile->storeAs('public/resumes', $filename);
            $fileUrl = '/storage/resumes/'.$filename;

            $resume = Resume::create([
                'userId' => $userId,
                'filename' => $cvFile->getClientOriginalName(),
                'fileUrl' => $fileUrl,
                'contactDetails' => 'N/A',
                'education' => 'N/A',
                'experience' => 'N/A',
                'skills' => 'N/A',
                'summary' => 'Uploaded resume file during application.',
            ]);

            $resumeId = $resume->id;
        }

        // FALLBACK: If only resume_id provided (no cv_file), validate and use existing resume
        if (! $cvFile && $providedResumeId) {
            $resume = Resume::find($providedResumeId);

            if (! $resume || $resume->userId !== $userId) {
                return $this->error('Invalid resume. Resume must belong to the applying user.', null, 422);
            }

            // Ensure resume has an uploaded file (not auto-generated)
            if (! $resume->fileUrl || strpos($resume->fileUrl, 'auto-generated') !== false) {
                return $this->error('Invalid resume file. Upload a real CV before applying.', null, 422);
            }

            $resumeId = $providedResumeId;
        }

        // Prevent duplicate applications by same user to same vacancy
        if (JobApplication::where('userId', $userId)->where('jobVacancyId', $jobVacancyId)->exists()) {
            return $this->error('You have already applied to this vacancy.', null, 409);
        }

        $application = JobApplication::create([
            'status' => 'pending',
            'aiGeneratedScore' => 0,
            'aiGeneratedFeedback' => null,
            'userId' => $userId,
            'resumeId' => $resumeId,
            'jobVacancyId' => $jobVacancyId,
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
