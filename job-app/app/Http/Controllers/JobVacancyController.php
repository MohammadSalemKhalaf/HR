<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\JobVacancy;
use App\Models\JobApplication;
use App\Models\Resume;
use App\Http\Requests\ApplyJobRequest;
use App\Services\ResumeAnalysisService;
use Throwable;
class JobVacancyController extends Controller
{
    public function __construct(private ResumeAnalysisService $resumeAnalysisService)
    {
    }

    public function show($id)
    {
        $job = JobVacancy::findOrFail($id);
        return view('job.show', compact('job'));
    }

    public function apply($id)
    {
        $job = JobVacancy::findOrFail($id);
        $user = auth()->user();
        return view('job.apply', compact('job', 'user'));
    }


    public function storeApplication(ApplyJobRequest $request, $id)
    {
        $job  = JobVacancy::findOrFail($id);
        $user = auth()->user();

        $request->validated();

        $resumeId = $request->input('resume_id');
        $uploadedPath = null;

        if (empty($resumeId) && !$request->hasFile('resume_file')) {
            return back()->withErrors(['resume_file' => 'Please select an existing resume or upload a new one.']);
        }

        $existingApplication = JobApplication::where('userId', $user->id)
            ->where('jobVacancyId', $job->id)
            ->exists();

        if ($existingApplication) {
            return back()->withErrors(['resume_id' => 'You have already applied for this job!']);
        }

        $selectedResume = null;

        if ($request->hasFile('resume_file')) {
            $file = $request->file('resume_file');
            $uploadedPath = Storage::disk('cloud')->putFile('resumes', $file);

            if (!$uploadedPath) {
                return back()->withErrors(['resume_file' => 'Cloud upload failed. Please try again.']);
            }

            // Prevent any accidentally generated placeholder paths from being used
            if (str_contains($uploadedPath, 'auto-generated')) {
                Storage::disk('cloud')->delete($uploadedPath);
                return back()->withErrors(['resume_file' => 'Invalid upload path. Please try a different file.']);
            }

            $selectedResume = new Resume([
                'filename'       => $file->getClientOriginalName(),
                'fileUrl'        => $uploadedPath,
                'contactDetails' => '',
                'education'      => '',
                'experience'     => '',
                'skills'         => '',
                'summary'        => '',
                'userId'         => $user->id,
            ]);
        } elseif (!empty($resumeId)) {
            $selectedResume = $user->resumes()->where('id', $resumeId)->first();

            if (!$selectedResume) {
                return back()->withErrors(['resume_id' => 'The selected resume is invalid.']);
            }

            // Disallow placeholder or auto-generated resumes
            if (!$selectedResume->fileUrl || str_contains($selectedResume->fileUrl, 'auto-generated')) {
                return back()->withErrors(['resume_id' => 'The selected resume must be a real uploaded file.']);
            }
        }

        try {
            if (!$selectedResume) {
                throw new \RuntimeException('No resume was resolved for this application.');
            }

            $resumeSource = $selectedResume->fileUrl;
            $extractedResumeData = $this->resumeAnalysisService->extractResumeInformation($resumeSource);
            $applicationAiData = $this->generateJobMatchFeedback($extractedResumeData, $job);

            Log::info('Resume job-match analysis result.', [
                'user_id' => $user->id,
                'job_id' => $job->id,
                'resume_path' => $resumeSource,
                'analysis' => $extractedResumeData,
                'job_match' => $applicationAiData,
            ]);

            DB::transaction(function () use ($user, $job, $selectedResume, $extractedResumeData, $applicationAiData, &$resumeId) {
                $resumeRecord = $selectedResume;

                if ($resumeRecord->exists) {
                    $resumeRecord->fill([
                        'education' => $extractedResumeData['education'],
                        'experience' => $extractedResumeData['experience'],
                        'skills' => $extractedResumeData['skills'],
                        'summary' => $extractedResumeData['summary'],
                    ]);
                    $resumeRecord->save();
                } else {
                    $resumeRecord = Resume::create([
                        'filename' => $resumeRecord->filename,
                        'fileUrl' => $resumeRecord->fileUrl,
                        'contactDetails' => $resumeRecord->contactDetails,
                        'education' => $extractedResumeData['education'],
                        'experience' => $extractedResumeData['experience'],
                        'skills' => $extractedResumeData['skills'],
                        'summary' => $extractedResumeData['summary'],
                        'userId' => $user->id,
                    ]);
                }

                $resumeId = $resumeRecord->id;

                JobApplication::create([
                    'userId' => $user->id,
                    'jobVacancyId' => $job->id,
                    'resumeId' => $resumeId,
                    'status' => 'pending',
                    'aiGeneratedScore' => $applicationAiData['score'],
                    'aiGeneratedFeedback' => $applicationAiData['feedback'],
                ]);
            });
        } catch (Throwable $e) {
            if ($uploadedPath) {
                Storage::disk('cloud')->delete($uploadedPath);
            }

            Log::error('Job application submission failed during resume upload or analysis.', [
                'user_id' => $user?->id,
                'job_id' => $job?->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['resume_file' => 'We could not process your resume. Please try again.']);
        }

        return redirect()->route('job-applications.index')
                         ->with('success', 'Application submitted successfully!');
    }

    private function generateJobMatchFeedback(array $analysis, JobVacancy $job): array
    {
        $apiKey = env('GROQ_API_KEY');

        if (!$apiKey) {
            Log::warning('GROQ_API_KEY is missing. Cannot perform AI job-match analysis.', [
                'job_id' => $job->id,
            ]);
            return [
                'score' => 0,
                'feedback' => 'Job-match analysis unavailable. API key not configured.',
            ];
        }

        $resumeText = $this->buildResumeText($analysis);
        $jobText = $this->buildJobText($job);

        try {
            $prompt = <<<'PROMPT'
Analyze how well a candidate's resume matches a job vacancy.

Resume Content:
{{RESUME}}

Job Vacancy:
{{JOB}}

Based on the resume content (skills, experience, education, summary) and the job requirements (title, description), determine:

1. A match score from 0 to 10 (0 = no match, 5 = moderate match, 10 = excellent match)
2. Concise feedback mentioning:
   - Which specific skills or experience from the resume match the job
   - Which job requirements are missing from the resume (if any)
   - Overall assessment of fit

Return ONLY valid JSON with exactly these keys (no markdown, no code fences):
{
  "score": <number 0-10>,
  "feedback": "<string with specific match analysis>"
}

Rules:
- Score must be a number between 0 and 10
- Feedback must mention specific skills or experience
- Feedback must be realistic and based on the actual resume content and job requirements
- Do NOT use generic feedback
- Keep feedback concise (2-3 sentences)
PROMPT;

            $prompt = str_replace('{{RESUME}}', $resumeText, $prompt);
            $prompt = str_replace('{{JOB}}', $jobText, $prompt);

            $response = Http::timeout(60)
                ->retry(2, 500)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
                    'temperature' => 0.3,
                    'max_tokens' => 500,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Groq job-match analysis failed.', [
                    'status' => $response->status(),
                    'job_id' => $job->id,
                    'body' => $response->json() ?? $response->body(),
                ]);

                return [
                    'score' => 0,
                    'feedback' => 'Job-match analysis failed. Please try again later.',
                ];
            }

            $content = $response->json('choices.0.message.content');

            if (!is_string($content) || trim($content) === '') {
                Log::warning('Groq job-match analysis returned empty response.', [
                    'job_id' => $job->id,
                ]);

                return [
                    'score' => 0,
                    'feedback' => 'Job-match analysis returned no results.',
                ];
            }

            $decoded = json_decode($this->sanitizeJson($content), true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                Log::error('Groq job-match analysis returned invalid JSON.', [
                    'job_id' => $job->id,
                    'content' => $content,
                    'json_error' => json_last_error_msg(),
                ]);

                return [
                    'score' => 0,
                    'feedback' => 'Job-match analysis parsing failed.',
                ];
            }

            $score = $this->normalizeScore($decoded['score'] ?? 0);
            $feedback = $this->normalizeFeedback($decoded['feedback'] ?? '');

            if ($feedback === '') {
                Log::warning('Groq job-match analysis feedback was empty.', [
                    'job_id' => $job->id,
                ]);

                return [
                    'score' => $score,
                    'feedback' => 'Job-match analysis completed with no detailed feedback.',
                ];
            }

            return [
                'score' => $score,
                'feedback' => $feedback,
            ];
        } catch (\Exception $e) {
            Log::error('Exception during job-match AI analysis.', [
                'job_id' => $job->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'score' => 0,
                'feedback' => 'Job-match analysis encountered an error.',
            ];
        }
    }

    private function buildResumeText(array $analysis): string
    {
        $parts = [];
        if (!empty($analysis['summary'])) {
            $parts[] = "Summary: " . $analysis['summary'];
        }
        if (!empty($analysis['skills'])) {
            $parts[] = "Skills: " . $analysis['skills'];
        }
        if (!empty($analysis['experience'])) {
            $parts[] = "Experience: " . $analysis['experience'];
        }
        if (!empty($analysis['education'])) {
            $parts[] = "Education: " . $analysis['education'];
        }

        return implode("\n\n", $parts);
    }

    private function buildJobText(JobVacancy $job): string
    {
        $parts = [];
        $parts[] = "Title: " . $job->title;
        if (!empty($job->description)) {
            $parts[] = "Description: " . $job->description;
        }
        if (!empty($job->type)) {
            $parts[] = "Type: " . $job->type;
        }
        if (!empty($job->location)) {
            $parts[] = "Location: " . $job->location;
        }

        return implode("\n\n", $parts);
    }

    private function normalizeScore(mixed $score): float
    {
        if (!is_numeric($score)) {
            return 0;
        }
        $num = floatval($score);
        return min(10, max(0, round($num, 1)));
    }

    private function normalizeFeedback(mixed $feedback): string
    {
        if (!is_string($feedback)) {
            return '';
        }

        $trimmed = trim($feedback);

        if (strlen($trimmed) === 0) {
            return '';
        }

        // Reject generic fallback feedback patterns
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

    private function buildApplicationAiData(array $analysis): array
    {
        $fields = [
            $analysis['summary'] ?? '',
            $analysis['skills'] ?? '',
            $analysis['experience'] ?? '',
            $analysis['education'] ?? '',
        ];

        $filledFields = count(array_filter($fields, static fn ($value) => trim((string) $value) !== ''));
        $score = round(($filledFields / 4) * 10, 1);

        $feedback = match ($filledFields) {
            4 => 'Resume analysis is complete and ready for review.',
            3 => 'Resume analysis is mostly complete and ready for review.',
            2 => 'Resume analysis is partial. The candidate profile may need more detail.',
            1 => 'Resume analysis returned limited information.',
            default => 'Resume analysis returned no usable information.',
        };

        return [
            'score' => $score,
            'feedback' => $feedback,
        ];
    }



        public function testGroqApi()
    {
        try {
            $apiKey = env('GROQ_API_KEY');

            if (!$apiKey) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'GROQ_API_KEY not found in .env file'
                ], 400);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type'  => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => 'llama-3.3-70b-versatile',
                'messages'    => [
                    [
                        'role'    => 'user',
                        'content' => "Hello, respond with 'GROQ API is working!'"
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens'  => 100,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'API request failed',
                    'details' => $response->json()
                ], $response->status());
            }

            $data = $response->json();
            $aiResponse = $data['choices'][0]['message']['content'] ?? 'No response content';

            return response()->json([
                'status'   => 'success',
                'message'  => 'GROQ API is working',
                'response' => $aiResponse,
                'full_data'=> $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Exception occurred',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
