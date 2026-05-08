<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Notifications\TaskAssigned;
use Illuminate\Support\Facades\Notification;

class TestEmailController extends Controller
{
    /**
     * Send test email to verify SMTP configuration
     * Restricted to authenticated company users
     */
    public function sendTestEmail(Request $request)
    {
        $user = $request->user();

        // Verify user is authenticated
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        try {
            // Create a simple test mailable
            Mail::to($user->email)
                ->send(new class extends \Illuminate\Mail\Mailable {
                    public function build()
                    {
                        return $this->subject('Test Email from Karaaj EMS')
                            ->view('emails.test')
                            ->with(['username' => $this->user->name ?? 'User'])
                            ->from(config('mail.from.address'), config('mail.from.name'));
                    }

                    public function __construct(public $user) {}
                });

            return back()->with('success', 'Test email sent successfully to ' . $user->email . '! Check your inbox.');
        } catch (\Exception $e) {
            \Log::error('Test email failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Send sample task notification
     */
    public function sendSampleTaskNotification(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }

        try {
            // Get or create a sample task for demo
            $employee = Employee::where('user_id', $user->id)->first();

            if (!$employee) {
                return back()->with('error', 'No employee profile found for your account.');
            }

            // Find an existing task or create a sample one
            $task = EmployeeTask::where('employee_id', $employee->id)->first();

            if (!$task) {
                return back()->with('error', 'No tasks assigned to you. Create a task first to test notifications.');
            }

            // Send the notification
            Notification::send($user, new TaskAssigned($task));

            return back()->with('success', 'Sample task notification sent! Check your email.');
        } catch (\Exception $e) {
            \Log::error('Sample notification failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send notification: ' . $e->getMessage());
        }
    }
}
