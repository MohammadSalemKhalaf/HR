<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JobApplicationController extends BaseApiController
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
        $query = JobApplication::with(['user', 'resume', 'jobvacancy.company'])->latest();
        $companyId = $this->resolveCompanyId($request);

        if ($companyId !== '') {
            $query->whereHas('jobvacancy', fn ($builder) => $builder->where('companyId', $companyId));
        }

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
        }

        return $this->success('Applications retrieved successfully.', $query->paginate(10));
    }

    public function show(Request $request, string $id): JsonResponse
    {
        $application = JobApplication::with(['user', 'resume', 'jobvacancy.company'])->withTrashed()->find($id);

        if (! $application) {
            return $this->notFound('Application not found.');
        }

        $companyId = $this->resolveCompanyId($request);

        if ($companyId !== '' && $application->jobvacancy?->companyId !== $companyId) {
            return $this->notFound('Application not found.');
        }

        return $this->success('Application retrieved successfully.', $application);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'job_vacancy_id' => ['required', 'exists:job_vacancies,id'],
            'resume_id' => ['nullable'],
            'cv_file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'cv_text' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $userId = $request->string('user_id');
        $jobVacancyId = $request->string('job_vacancy_id');
        $providedResumeId = $request->input('resume_id');
        $cvFile = $request->file('cv_file');
        $cvText = trim((string) $request->input('cv_text', ''));
    // Convert Stringable objects to plain strings
    $userId = (string) $userId;
    $jobVacancyId = (string) $jobVacancyId;


        // Require at least one: resume_id, cv_file, or cv_text
        if (! $providedResumeId && ! $cvFile && $cvText === '') {
            return $this->error('Either resume_id, cv_file, or cv_text must be provided.', null, 422);
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
                'summary' => $cvText !== '' ? Str::limit($cvText, 255, '') : 'Uploaded resume file during application.',
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

        $job = JobVacancy::with(['company', 'jobcategory'])->find($jobVacancyId);

        if (! $job) {
            return $this->notFound('Job vacancy not found.');
        }

        $analysisText = $cvText;

        if ($analysisText === '' && $resumeId) {
            $resumeRecord = Resume::find($resumeId);

            if ($resumeRecord) {
                $analysisText = implode("\n\n", array_filter([
                    $resumeRecord->summary ?? '',
                    $resumeRecord->skills ?? '',
                    $resumeRecord->experience ?? '',
                    $resumeRecord->education ?? '',
                ]));
            }
        }

        $aiData = $this->generateJobMatchFeedback($analysisText, $job);

        // Gate application submission by minimum skills match score.
        if (($aiData['score'] ?? 0) < 5) {
            return $this->error(
                'Application blocked: your current profile does not meet the minimum required skills for this job (minimum score is 5/10).',
                [
                    'ai_score' => [$aiData['score'] ?? 0],
                    'ai_feedback' => [$aiData['feedback'] ?? ''],
                ],
                422
            );
        }

        $application = JobApplication::create([
            'status' => 'pending',
            'aiGeneratedScore' => $aiData['score'],
            'aiGeneratedFeedback' => $aiData['feedback'],
            'userId' => $userId,
            'resumeId' => $resumeId,
            'jobVacancyId' => $jobVacancyId,
        ]);

        return $this->success('Application created successfully.', $application->load(['user', 'resume', 'jobvacancy.company', 'jobvacancy.jobcategory']), 201);
    }

    public function analyze(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'job_vacancy_id' => ['required', 'exists:job_vacancies,id'],
            'cv_text' => ['required', 'string', 'min:20'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $job = JobVacancy::with(['company', 'jobcategory'])->find($request->string('job_vacancy_id'));

        if (! $job) {
            return $this->notFound('Job vacancy not found.');
        }

        $analysis = $this->generateJobMatchFeedback(trim((string) $request->input('cv_text', '')), $job);

        return $this->success('Application analysis generated successfully.', $analysis);
    }

    public function accept(string $id): JsonResponse
    {
        $application = JobApplication::with('jobvacancy')->find($id);

        if (! $application) {
            return $this->notFound('Application not found.');
        }

        $companyId = $this->resolveCompanyId(request());

        if ($companyId !== '' && $application->jobvacancy?->companyId !== $companyId) {
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
        $application = JobApplication::with('jobvacancy')->find($id);

        if (! $application) {
            return $this->notFound('Application not found.');
        }

        $companyId = $this->resolveCompanyId(request());

        if ($companyId !== '' && $application->jobvacancy?->companyId !== $companyId) {
            return $this->notFound('Application not found.');
        }

        $application->update(['status' => 'rejected']);

        return $this->success('Application rejected successfully.', $application->fresh(['user', 'resume', 'jobvacancy.company']));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $application = JobApplication::with('jobvacancy')->find($id);

        if (! $application) {
            return $this->notFound('Application not found.');
        }

        $companyId = $this->resolveCompanyId($request);

        if ($companyId !== '' && $application->jobvacancy?->companyId !== $companyId) {
            return $this->notFound('Application not found.');
        }

        $validator = Validator::make($request->all(), [
            'status' => ['required', 'in:pending,accepted,rejected'],
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed.', $validator->errors(), 422);
        }

        $application->update(['status' => $request->input('status')]);

        return $this->success('Application updated successfully.', $application->fresh(['user', 'resume', 'jobvacancy.company']));
    }

    private function generateJobMatchFeedback(string $resumeText, JobVacancy $job): array
    {
        $localAnalysis = $this->buildApplicationAiData($resumeText, $job);
        $apiKey = env('GROQ_API_KEY');

        if ($apiKey) {
            try {
                $prompt = <<<'PROMPT'
Compare the candidate resume with the job vacancy and return a strict JSON object.

Candidate Resume:
{{RESUME}}

Job Vacancy:
{{JOB}}

Evaluate the fit using actual overlapping skills, tools, and experience.

Return ONLY valid JSON in this exact shape:
{
  "score": 0-10,
  "feedback": "2-3 concise sentences that mention matched skills, missing skills, and overall fit"
}

Rules:
- The score must reflect the actual match between resume and job requirements.
- Mention specific skills that matched and specific gaps that are missing.
- Do not use generic phrases like 'good candidate' or 'ready for review'.
- Be strict and evidence-based: only claim matches that appear in the resume text.
PROMPT;

                $prompt = str_replace('{{RESUME}}', $resumeText, $prompt);
                $prompt = str_replace('{{JOB}}', $this->buildJobText($job), $prompt);

                $response = Http::timeout(45)
                    ->retry(2, 500)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
                        'temperature' => 0.2,
                        'max_tokens' => 400,
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => $prompt,
                            ],
                        ],
                    ]);

                if ($response->successful()) {
                    $content = $response->json('choices.0.message.content');

                    if (is_string($content) && trim($content) !== '') {
                        $decoded = json_decode($this->sanitizeJson($content), true);

                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $feedback = $this->normalizeFeedback($decoded['feedback'] ?? '');

                            if ($feedback !== '') {
                                return [
                                    'score' => $this->normalizeScore($decoded['score'] ?? 0),
                                    'feedback' => $feedback,
                                    'matched_skills' => $localAnalysis['matched_skills'] ?? [],
                                    'missing_skills' => $localAnalysis['missing_skills'] ?? [],
                                    'analysis_source' => 'groq',
                                ];
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Fall through to local matching.
            }
        }

        return $localAnalysis;
    }

    private function buildApplicationAiData(string $resumeText, JobVacancy $job): array
    {
        $normalizedResume = $this->normalizeText($resumeText);
        $normalizedJob = $this->normalizeText($this->buildJobText($job));

        $resumeSkills = $this->extractSkills($normalizedResume);
        $jobSkills = $this->extractSkills($normalizedJob);

        $matchedSkills = array_values(array_intersect($jobSkills, $resumeSkills));
        $missingSkills = array_values(array_diff($jobSkills, $resumeSkills));

        $jobRequirements = $this->extractRequirementTerms($normalizedJob);
        $matchedRequirements = array_values(array_filter($jobRequirements, fn ($term) => str_contains($normalizedResume, $term)));
        $missingRequirements = array_values(array_filter($jobRequirements, fn ($term) => ! str_contains($normalizedResume, $term)));

        $skillCoverage = count($jobSkills) > 0 ? count($matchedSkills) / count($jobSkills) : 0;
        $requirementCoverage = count($jobRequirements) > 0 ? count($matchedRequirements) / count($jobRequirements) : 0;

        $resumeLengthBoost = min(1.2, max(0, strlen($normalizedResume) / 2200));
        $score = round(min(10, max(0, ($skillCoverage * 5.5) + ($requirementCoverage * 3.3) + $resumeLengthBoost)), 1);

        if (count($matchedSkills) === 0 && strlen(trim($resumeText)) > 0) {
            $score = min($score, 4.5);
        }

        $feedbackParts = [];

        if ($matchedSkills !== []) {
            $feedbackParts[] = 'Matched skills: ' . implode(', ', array_slice($matchedSkills, 0, 6)) . '.';
        }

        if ($missingSkills !== []) {
            $feedbackParts[] = 'Missing key skills: ' . implode(', ', array_slice($missingSkills, 0, 5)) . '.';
        }

        if ($matchedRequirements !== []) {
            $feedbackParts[] = 'Matched requirements: ' . implode(', ', array_slice($matchedRequirements, 0, 4)) . '.';
        }

        if ($missingRequirements !== []) {
            $feedbackParts[] = 'Missing requirements: ' . implode(', ', array_slice($missingRequirements, 0, 4)) . '.';
        }

        if ($feedbackParts === []) {
            $feedbackParts[] = 'The resume provides limited evidence of a direct match with this role.';
        }

        $feedbackParts[] = $score >= 7
            ? 'Overall, this is a strong match for the role.'
            : ($score >= 5 ? 'Overall, this is a partial match and would benefit from a few more relevant skills.' : 'Overall, this is a weak match for the role as written.');

        return [
            'score' => $score,
            'feedback' => implode(' ', $feedbackParts),
            'matched_skills' => $matchedSkills,
            'missing_skills' => $missingSkills,
            'analysis_source' => 'deterministic',
        ];
    }

    private function extractRequirementTerms(string $jobText): array
    {
        $stopWords = [
            'the', 'and', 'for', 'with', 'from', 'that', 'this', 'you', 'your', 'our', 'role', 'job',
            'work', 'will', 'are', 'have', 'has', 'must', 'should', 'experience', 'candidate', 'team',
            'skills', 'requirements', 'position', 'strong', 'good', 'ability', 'knowledge', 'using'
        ];

        $tokens = preg_split('/\s+/', $jobText) ?: [];
        $filtered = [];

        foreach ($tokens as $token) {
            $term = trim($token);
            if ($term === '' || strlen($term) < 4) {
                continue;
            }
            if (in_array($term, $stopWords, true)) {
                continue;
            }
            $filtered[] = $term;
        }

        return array_values(array_slice(array_unique($filtered), 0, 20));
    }

    private function extractSkills(string $text): array
    {
        $haystack = $this->normalizeText($text);

        $skills = [
            'php', 'laravel', 'vue', 'react', 'angular', 'javascript', 'typescript', 'node', 'node.js',
            'html', 'css', 'tailwind', 'bootstrap', 'sql', 'mysql', 'postgres', 'postgresql', 'mongodb',
            'api', 'rest', 'graphql', 'docker', 'kubernetes', 'aws', 'git', 'testing', 'unit testing',
            'leadership', 'communication', 'problem solving', 'project management', 'figma', 'ui ux',
            'devops', 'ci cd', 'agile', 'scrum', 'python', 'java', 'c#', 'dotnet', 'mobile', 'android', 'ios'
        ];

        $matched = [];

        foreach ($skills as $skill) {
            if (str_contains($haystack, $skill)) {
                $matched[] = $skill;
            }
        }

        return array_values(array_unique($matched));
    }

    private function normalizeText(string $text): string
    {
        $normalized = strtolower($text);
        $normalized = preg_replace('/[^a-z0-9\+\#\.\-\s]/', ' ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        return trim($normalized);
    }

    private function buildJobText(JobVacancy $job): string
    {
        $parts = [];
        $parts[] = 'Title: ' . $job->title;

        if (! empty($job->description)) {
            $parts[] = 'Description: ' . $job->description;
        }

        if (! empty($job->location)) {
            $parts[] = 'Location: ' . $job->location;
        }

        if (! empty($job->type)) {
            $parts[] = 'Type: ' . $job->type;
        }

        if (! empty($job->jobcategory?->name)) {
            $parts[] = 'Category: ' . $job->jobcategory->name;
        }

        if (! empty($job->company?->name)) {
            $parts[] = 'Company: ' . $job->company->name;
        }

        return implode("\n\n", $parts);
    }

    private function normalizeScore(mixed $score): float
    {
        if (! is_numeric($score)) {
            return 0;
        }

        $num = (float) $score;

        if ($num > 10 && $num <= 100) {
            $num /= 10;
        }

        return min(10, max(0, round($num, 1)));
    }

    private function normalizeFeedback(mixed $feedback): string
    {
        if (! is_string($feedback)) {
            return '';
        }

        $trimmed = trim($feedback);

        if ($trimmed === '') {
            return '';
        }

        $genericPatterns = [
            'Resume analysis is complete',
            'Resume analysis is',
            'Analysis completed',
            'Good candidate',
            'ready for review',
        ];

        foreach ($genericPatterns as $pattern) {
            if (stripos($trimmed, $pattern) !== false) {
                return '';
            }
        }

        return $trimmed;
    }

    private function sanitizeJson(string $content): string
    {
        $trimmed = trim($content);

        if (str_starts_with($trimmed, '```')) {
            $trimmed = preg_replace('/^```(?:json)?\s*/', '', $trimmed) ?? $trimmed;
            $trimmed = preg_replace('/\s*```$/', '', $trimmed) ?? $trimmed;
        }

        return trim($trimmed);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $application = JobApplication::with('jobvacancy')->find($id);

        if (! $application) {
            return $this->notFound('Application not found.');
        }

        $companyId = $this->resolveCompanyId($request);

        if ($companyId !== '' && $application->jobvacancy?->companyId !== $companyId) {
            return $this->notFound('Application not found.');
        }

        $application->delete();

        return $this->success('Application archived successfully.');
    }

    public function restore(Request $request, string $id): JsonResponse
    {
        $application = JobApplication::with(['jobvacancy'])->withTrashed()->find($id);

        if (! $application) {
            return $this->notFound('Application not found.');
        }

        $companyId = $this->resolveCompanyId($request);

        if ($companyId !== '' && $application->jobvacancy?->companyId !== $companyId) {
            return $this->notFound('Application not found.');
        }

        if (! $application->trashed()) {
            return $this->error('Application is not archived.', [], 422);
        }

        $application->restore();

        return $this->success('Application restored successfully.', $application->fresh(['user', 'resume', 'jobvacancy.company']));
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
