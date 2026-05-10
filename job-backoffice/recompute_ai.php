<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JobApplication;
use App\Models\Resume;

$updated = 0;
foreach (JobApplication::all() as $application) {
    $aiScore = 0;
    $aiFeedback = null;

    if ($application->resumeId) {
        $resume = Resume::find($application->resumeId);
        if ($resume) {
            $fields = [
                $resume->summary ?? '',
                $resume->skills ?? '',
                $resume->experience ?? '',
                $resume->education ?? '',
            ];
            $filled = array_filter($fields, static fn($v) => trim((string)$v) !== '');
            $filledCount = count($filled);
            $aiScore = round(($filledCount / 4) * 10, 1);
            switch ($filledCount) {
                case 4:
                    $aiFeedback = 'Resume analysis is complete and ready for review.';
                    break;
                case 3:
                    $aiFeedback = 'Resume analysis is mostly complete and ready for review.';
                    break;
                case 2:
                    $aiFeedback = 'Resume analysis is partial. The candidate profile may need more detail.';
                    break;
                case 1:
                    $aiFeedback = 'Resume analysis returned limited information.';
                    break;
                default:
                    $aiFeedback = 'Resume analysis returned no usable information.';
            }
        }
    }

    $application->aiGeneratedScore = $aiScore;
    $application->aiGeneratedFeedback = $aiFeedback;
    $application->save();
    $updated++;
}

echo "Updated $updated application(s)\n";
