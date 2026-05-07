<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobApplicationsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $applications = JobApplication::where('userId', $user->id)
            ->with(['jobvacancy.company', 'resume'])
            ->latest('created_at')
            ->get();

        return view('job-applications.index', compact('applications'));
    }
}
