<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobApplicationsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            abort(403);
        }

        $applications = JobApplication::where('userId', $user->id)
            ->with(['jobvacancy.company', 'resume'])
            ->latest('created_at')
            ->get();

        return view('job-applications.index', compact('applications'));
    }
}
