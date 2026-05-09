# Enterprise Email Notification System - Complete Implementation Guide

## 📋 System Overview

The Karaaj HR-SaaS Enterprise Email Notification System is a comprehensive, production-grade notification infrastructure that handles employee and manager communications for tasks, leaves, attendance, and weekly reports. The system uses Laravel Notifications with Mailable classes, professional HTML templates, and database-backed queue support.

## 🏗️ Architecture

### Components

```
┌─────────────────────────────────────────┐
│         Notification Trigger            │
│   (Controller/Command/Job)              │
└────────────┬────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────┐
│      Notification Class                 │
│   (TaskAssigned, LeaveApproved, etc)    │
└────────────┬────────────────────────────┘
             │
             ▼
┌─────────────────────────────────────────┐
│      Channel Resolution                 │
│   (via() returns channels)              │
│   - Database channel (in-app)           │
│   - Mail channel (email)                │
│   - Queue support (async)               │
└────────────┬────────────────────────────┘
             │
        ┌────┴────────────────────────┐
        │                             │
        ▼                             ▼
┌──────────────────┐        ┌──────────────────┐
│  Database Store  │        │  Mail Queue      │
│  Notification    │        │  (jobs table)    │
│  Preferences     │        │  → SMTP Server   │
└──────────────────┘        └──────────────────┘
```

## 📁 File Structure

```
job-backoffice/
├── app/
│   ├── Notifications/
│   │   ├── TaskAssigned.php
│   │   ├── TaskCompleted.php
│   │   ├── LeaveRequested.php
│   │   ├── LeaveApproved.php
│   │   ├── LeaveRejected.php
│   │   └── (Weekly reports - future)
│   ├── Models/
│   │   └── NotificationPreference.php
│   ├── Services/
│   │   └── (Notification services)
│   └── Http/
│       ├── Controllers/
│       │   ├── TestEmailController.php
│       │   └── NotificationPreferenceController.php (future)
├── database/
│   └── migrations/
│       └── 2026_05_08_000012_create_notification_preferences_table.php
├── resources/
│   └── views/
│       └── emails/
│           ├── layouts/
│           │   └── app.blade.php
│           ├── task-assigned.blade.php
│           ├── task-completed.blade.php
│           ├── leave-requested.blade.php
│           ├── leave-approved.blade.php
│           └── leave-rejected.blade.php
└── routes/
    └── web.php (test-email routes)
```

## 🔧 Configuration

### Environment Variables (.env)
```env
# Mail Provider: Gmail SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=mohammadkhalaf3hfg@gmail.com
MAIL_PASSWORD="gkql nrvc wksw frnx"  # Note: spaces require quotes
MAIL_FROM_ADDRESS=mohammadkhalaf3hfg@gmail.com
MAIL_FROM_NAME="Karaaj(HR-SaaS)"

# Queue Driver: Database-backed for async processing
QUEUE_CONNECTION=database
```

### Key Configuration Files
- `config/mail.php` - Mail driver configuration
- `config/queue.php` - Queue connection settings
- `.env` - Environment-specific secrets

## 📧 Notification Classes

### 1. TaskAssigned Notification

**Triggers:** When manager assigns task to employee

**Channels:** Database + Mail

**Template:** `emails.task-assigned`

**Example:**
```php
// In Manager/TaskController
$task = EmployeeTask::create([...]);
$task->employee->user->notify(new TaskAssigned($task));
```

**Email Contains:**
- Task title and description
- Priority badge (HIGH/MEDIUM/LOW)
- Due date with relative time
- Repository link (if applicable)
- Manager name

### 2. TaskCompleted Notification

**Triggers:** When employee marks task as complete

**Channels:** Database + Mail

**Template:** `emails.task-completed`

**Example:**
```php
// In EmployeeArea/TaskController
$task->update(['completed_at' => now()]);
$task->manager->user->notify(new TaskCompleted($task));
```

**Email Contains:**
- Task title
- Employee name
- Completion time
- Priority level
- "Review Task" action button

### 3. LeaveRequested Notification

**Triggers:** When employee submits leave request

**Channels:** Database + Mail

**Template:** `emails.leave-requested`

**Example:**
```php
// In EmployeeArea/LeaveController
$leave = Leave::create([...]);
$leave->employee->manager->user->notify(new LeaveRequested($leave));
```

**Email Contains:**
- Leave type (Annual, Sick, Personal, etc)
- Start and end dates
- Total duration in days
- Leave reason
- "Review & Approve" action button

### 4. LeaveApproved Notification

**Triggers:** When manager approves leave request

**Channels:** Database + Mail

**Template:** `emails.leave-approved`

**Example:**
```php
// In Manager/LeaveController
$leave->update(['status' => 'approved', 'approved_by' => auth()->user()->employee->id]);
$leave->employee->user->notify(new LeaveApproved($leave));
```

**Email Contains:**
- Leave type and dates
- Total days approved
- Approver name
- Manager notes (if any)
- Success confirmation badge

### 5. LeaveRejected Notification

**Triggers:** When manager rejects leave request

**Channels:** Database + Mail

**Template:** `emails.leave-rejected`

**Example:**
```php
// In Manager/LeaveController
$leave->update(['status' => 'rejected', 'approved_by' => auth()->user()->employee->id]);
$leave->employee->user->notify(new LeaveRejected($leave));
```

**Email Contains:**
- Leave type and requested dates
- Rejection reason
- Manager name
- "Submit New Request" action button

## 🎨 Email Template System

### Master Layout: `emails.layouts.app`

All emails use a professional, responsive HTML template with:

**Features:**
- Karaaj HR-SaaS branded header with gradient
- Inline CSS for email client compatibility
- Responsive design (mobile-friendly)
- Themed cards (success, warning, danger, info)
- Badge styles (high, medium, low, success, pending, rejected)
- Info-row display for key-value data
- Professional footer with branding
- Timestamp display

**Usage:**
```blade
@component('emails.layouts.app', ['headerSubtitle' => 'Task Assignment'])
    <div class="greeting">Hello {{ $user->name }},</div>
    <div class="description">...</div>
    <div class="card info">
        <div class="info-row">
            <div class="info-label">Priority:</div>
            <div class="info-value"><span class="badge high">🔴 HIGH</span></div>
        </div>
    </div>
    <div class="button-wrapper">
        <a href="{{ url(...) }}" class="button">Action Button</a>
    </div>
@endcomponent
```

### Template Variables

Each template receives:

**TaskAssigned:**
- `$task` - EmployeeTask instance
- `$employee` - Employee instance
- `$manager` - Manager (Employee) instance
- `$company` - Company instance

**TaskCompleted:**
- `$task` - EmployeeTask instance
- `$manager` - Manager (Employee) instance
- `$employee` - Employee instance

**LeaveRequested:**
- `$leave` - Leave instance
- `$manager` - User instance (manager)

**LeaveApproved/Rejected:**
- `$leave` - Leave instance

## 🔄 Integration Points

### Task Management

**File:** `app/Http/Controllers/Manager/TaskController.php`

```php
// When creating task
public function store(StoreEmployeeTaskRequest $request)
{
    $task = EmployeeTask::create([...]);
    
    try {
        $task->employee->user->notify(new TaskAssigned($task));
    } catch (\Exception $e) {
        \Log::error('Task notification failed', ['error' => $e->getMessage()]);
    }
    
    return redirect()->route('manager.tasks.index');
}
```

**File:** `app/Http/Controllers/EmployeeArea/TaskController.php`

```php
// When completing task
public function update_status(Request $request, EmployeeTask $task)
{
    $task->update(['completed_at' => now()]);
    
    try {
        $task->manager->user->notify(new TaskCompleted($task));
    } catch (\Exception $e) {
        \Log::error('Task completion notification failed', ['error' => $e->getMessage()]);
    }
    
    return response()->json(['success' => true]);
}
```

### Leave Management

**File:** `app/Http/Controllers/EmployeeArea/LeaveController.php`

```php
public function store(StoreLeaveRequest $request)
{
    $leave = Leave::create([...]);
    
    try {
        $leave->employee->manager->user->notify(new LeaveRequested($leave));
    } catch (\Exception $e) {
        \Log::error('Leave request notification failed', ['error' => $e->getMessage()]);
    }
    
    return redirect()->route('employee.leaves.index');
}
```

**File:** `app/Http/Controllers/Manager/LeaveController.php`

```php
public function approve(Leave $leave)
{
    $leave->update([
        'status' => 'approved',
        'approved_by' => auth()->user()->employee->id
    ]);
    
    try {
        $leave->employee->user->notify(new LeaveApproved($leave));
    } catch (\Exception $e) {
        \Log::error('Leave approval notification failed', ['error' => $e->getMessage()]);
    }
    
    return back()->with('success', 'Leave approved');
}

public function reject(Leave $leave, Request $request)
{
    $leave->update([
        'status' => 'rejected',
        'approved_by' => auth()->user()->employee->id,
        'notes' => $request->notes
    ]);
    
    try {
        $leave->employee->user->notify(new LeaveRejected($leave));
    } catch (\Exception $e) {
        \Log::error('Leave rejection notification failed', ['error' => $e->getMessage()]);
    }
    
    return back()->with('success', 'Leave rejected');
}
```

## 🧪 Testing

### Test Routes

Two endpoints are provided for testing email functionality:

#### 1. Send Test Email
**Route:** `POST /test-email`

```bash
curl -X POST http://localhost/test-email \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -L
```

**Controller:** `TestEmailController@sendTestEmail`

**Function:**
- Sends a simple verification email to configured MAIL_FROM_ADDRESS
- Useful for SMTP connectivity testing

#### 2. Send Sample Task Notification
**Route:** `POST /test-email/sample-task`

```bash
curl -X POST http://localhost/test-email/sample-task \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -L
```

**Controller:** `TestEmailController@sendSampleTaskNotification`

**Function:**
- Triggers a sample TaskAssigned notification
- Sends to configured MAIL_FROM_ADDRESS
- Tests full notification pipeline

### Manual Testing Workflow

1. **Setup Test Users**
   ```bash
   php artisan tinker
   
   // Create manager
   $manager = Employee::factory()->create(['role' => 'manager']);
   $manager->user->email = 'manager@test.com';
   $manager->user->save();
   
   // Create employee
   $employee = Employee::factory()->create(['manager_id' => $manager->id, 'role' => 'employee']);
   $employee->user->email = 'employee@test.com';
   $employee->user->save();
   ```

2. **Test Task Assignment**
   ```php
   // Create and send task notification
   $task = EmployeeTask::create([
       'manager_employee_id' => $manager->id,
       'employee_id' => $employee->id,
       'title' => 'Test Task',
       'description' => 'This is a test task',
       'priority' => 'high',
       'due_date' => now()->addDays(5)
   ]);
   
   $employee->user->notify(new TaskAssigned($task));
   ```

3. **Test Leave Request**
   ```php
   $leave = Leave::create([
       'employee_id' => $employee->id,
       'type' => 'annual',
       'start_date' => now()->addDays(7),
       'end_date' => now()->addDays(10),
       'reason' => 'Vacation'
   ]);
   
   $manager->user->notify(new LeaveRequested($leave));
   ```

4. **Check Email Delivery**
   - Monitor SMTP logs: `tail -f storage/logs/laravel.log`
   - Check notification database: `SELECT * FROM notifications;`
   - Verify email provider (Gmail) activity log

## 🚀 Async Queue Processing

### Setup

1. **Database Queue Driver** is already configured in `.env`:
   ```env
   QUEUE_CONNECTION=database
   ```

2. **Jobs Table** exists from migration `0001_01_01_000002_create_jobs_table`

### Processing Queued Notifications

**Start Queue Worker:**
```bash
php artisan queue:work --timeout=60
```

**Monitor Queue:**
```bash
php artisan queue:retry all
```

### How It Works

When a notification is queued, Laravel will:
1. Store job in `jobs` table
2. Queue worker picks up jobs
3. Executes notification sending asynchronously
4. Deletes job from queue on success
5. Records failure in `failed_jobs` if error occurs

**Benefits:**
- Non-blocking: Requests complete immediately
- Reliable: Failed jobs are retried
- Scalable: Multiple workers can process jobs
- Observable: Queue status can be monitored

## 🔐 Notification Preferences (Future Enhancement)

### Model: NotificationPreference

**Location:** `app/Models/NotificationPreference.php`

**Schema:**
```php
$table->id();
$table->foreignId('user_id')->unique();
$table->boolean('email_task_assigned')->default(true);
$table->boolean('email_task_completed')->default(true);
$table->boolean('email_leave_requested')->default(true);
$table->boolean('email_leave_approval')->default(true);
$table->boolean('email_weekly_report')->default(true);
$table->boolean('email_activity_digest')->default(false);
$table->boolean('in_app_notifications')->default(true);
$table->timestamps();
```

**Helper Methods:**
```php
// Get or create preferences with defaults
$prefs = NotificationPreference::forUser($user);

// Check if user wants specific notification type
if ($prefs->wantsEmail('task_assigned')) {
    // Send email
}
```

**Future Integration:**
```php
public function via($notifiable) {
    $prefs = NotificationPreference::forUser($notifiable);
    $channels = ['database'];
    
    if ($prefs->wantsEmail('task_assigned')) {
        $channels[] = 'mail';
    }
    
    return $channels;
}
```

## 📊 Database Schema

### notification_preferences table
```sql
CREATE TABLE notification_preferences (
  id bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id bigint unsigned NOT NULL UNIQUE,
  email_task_assigned tinyint(1) NOT NULL DEFAULT '1',
  email_task_completed tinyint(1) NOT NULL DEFAULT '1',
  email_leave_requested tinyint(1) NOT NULL DEFAULT '1',
  email_leave_approval tinyint(1) NOT NULL DEFAULT '1',
  email_weekly_report tinyint(1) NOT NULL DEFAULT '1',
  email_activity_digest tinyint(1) NOT NULL DEFAULT '0',
  in_app_notifications tinyint(1) NOT NULL DEFAULT '1',
  created_at timestamp NULL,
  updated_at timestamp NULL,
  CONSTRAINT fk_notification_preferences_user_id 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### notifications table (Laravel default)
```sql
-- Stores in-app notifications
SELECT id, notifiable_type, notifiable_id, type, data, read_at, created_at FROM notifications;
```

### jobs table (Laravel Queue)
```sql
-- Stores queued notification jobs
SELECT id, queue, payload, attempts, reserved_at, available_at, created_at FROM jobs;
```

## 📝 Email Content Examples

### Task Assigned Email

**Subject:** New Task Assigned: Fix Login Bug

**Content:**
- Greeting to employee
- Manager name
- Task details: title, priority, due date, repository link
- Action button to view task
- Dashboard link

### Leave Request Email

**Subject:** Leave Request: John Smith

**Content:**
- Greeting to manager
- Employee name
- Leave details: type, dates, duration
- Reason for leave
- Action button to review
- Status badge: PENDING APPROVAL

### Leave Approved Email

**Subject:** Leave Request Approved

**Content:**
- Greeting to employee
- Confirmation message
- Leave details: type, dates, duration
- Approved by manager
- Status badge: APPROVED
- Encouragement message

## 🐛 Troubleshooting

### Emails Not Sending

1. **Check SMTP Configuration**
   ```bash
   php artisan tinker
   >> config('mail.mailer')  // should be 'smtp'
   >> config('mail.host')     // should be 'smtp.gmail.com'
   >> config('mail.port')     // should be 587
   ```

2. **Test SMTP Connection**
   ```bash
   php artisan mail:test mohammadkhalaf3hfg@gmail.com
   ```

3. **Check Queue Status**
   ```bash
   php artisan queue:failed  // shows failed jobs
   ```

4. **Review Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Gmail App Password Issues

- Gmail requires App-Specific Passwords (not main password)
- Generated at: https://myaccount.google.com/apppasswords
- Password must be quoted in .env if contains spaces: `"gkql nrvc wksw frnx"`
- Enable "Less secure app access" is NOT needed for App Passwords

### Database Connection Issues

- Verify MariaDB is running: `systemctl status mariadb`
- Check connection: `php artisan tinker >> DB::connection()->getPDO()`
- Verify migration: `php artisan migrate:status`

## 🎯 Next Steps

### Phase 4: Advanced Features

1. **Weekly Reports**
   - Summarize tasks completed, attendance, hours worked
   - Optional AI-powered insights via Groq API
   - Scheduled via Laravel Scheduler

2. **User Preferences UI**
   - Settings page for notification preferences
   - Toggle email/in-app notifications per type
   - Notification history

3. **Activity Digest**
   - Daily or weekly activity summaries
   - Optional batch notifications
   - Digest of all events

4. **Enhanced Analytics**
   - Notification delivery metrics
   - Email open rates
   - User engagement tracking

## 📚 References

- [Laravel Notifications Documentation](https://laravel.com/docs/11.x/notifications)
- [Laravel Mail Documentation](https://laravel.com/docs/11.x/mail)
- [Laravel Queue Documentation](https://laravel.com/docs/11.x/queues)
- [Gmail App Passwords Setup](https://support.google.com/accounts/answer/185833)
- [SMTP Protocol RFC 5321](https://tools.ietf.org/html/rfc5321)

## 📞 Support

For issues or questions about the email notification system:

1. Check the troubleshooting section above
2. Review Laravel documentation links
3. Check application logs: `storage/logs/laravel.log`
4. Test with sample endpoints: `/test-email` and `/test-email/sample-task`

---

**System Status:** ✅ Ready for Production

**Last Updated:** {{ date('Y-m-d H:i:s') }}

**Version:** 1.0.0 (Enterprise Edition)
