# Email Notification System - Quick Reference

## 🚀 Quick Start

### Send a Notification

```php
// Import the notification class
use App\Notifications\TaskAssigned;

// Send to user
$user->notify(new TaskAssigned($task));

// Or notify multiple users
User::whereIn('id', [1, 2, 3])->each(fn($user) => 
    $user->notify(new TaskAssigned($task))
);
```

## 📧 Available Notifications

| Notification | Trigger | To | Via |
|---|---|---|---|
| **TaskAssigned** | Task created by manager | Employee | DB + Mail |
| **TaskCompleted** | Employee marks task done | Manager | DB + Mail |
| **LeaveRequested** | Employee requests leave | Manager | DB + Mail |
| **LeaveApproved** | Manager approves leave | Employee | DB + Mail |
| **LeaveRejected** | Manager rejects leave | Employee | DB + Mail |

## 🎨 Email Template Reference

### Using the Master Layout

```blade
@component('emails.layouts.app', ['headerSubtitle' => 'Your Subtitle'])
    {{-- Content goes here --}}
@endcomponent
```

### Common Components

**Greeting:**
```blade
<div class="greeting">Hello {{ $user->name }},</div>
```

**Description:**
```blade
<div class="description">Your message here</div>
```

**Info Card:**
```blade
<div class="card info">
    <div class="info-row">
        <div class="info-label">Label:</div>
        <div class="info-value">Value</div>
    </div>
</div>
```

**Badges:**
```blade
<!-- High Priority -->
<span class="badge high">🔴 HIGH</span>

<!-- Medium Priority -->
<span class="badge medium">🟡 MEDIUM</span>

<!-- Low Priority -->
<span class="badge low">🟢 LOW</span>

<!-- Success -->
<span class="badge success">✓ APPROVED</span>

<!-- Pending -->
<span class="badge pending">⏳ PENDING</span>

<!-- Rejected -->
<span class="badge rejected">✗ REJECTED</span>
```

**Action Button:**
```blade
<div class="button-wrapper">
    <a href="{{ url(...) }}" class="button">Click Me</a>
</div>
```

**Card Types:**
```blade
<div class="card info">        <!-- Blue left border -->
<div class="card success">     <!-- Green left border -->
<div class="card warning">     <!-- Orange left border -->
<div class="card danger">      <!-- Red left border -->
```

## 🔧 Configuration

### Environment (.env)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-gmail@gmail.com
MAIL_PASSWORD="app-specific-password"  # With spaces, use quotes
MAIL_FROM_ADDRESS=your-gmail@gmail.com
MAIL_FROM_NAME="App Name"

QUEUE_CONNECTION=database
```

### After Configuration Changes

```bash
# Clear config cache
php artisan config:cache

# Start queue worker (if using async)
php artisan queue:work
```

## 🧪 Testing

### Send Test Email
```bash
# Simple email test
php artisan mail:test your-email@example.com

# Or use web route
POST /test-email
POST /test-email/sample-task
```

### In Tinker
```bash
php artisan tinker

# Send test notification
$user = User::first();
$user->notify(new \App\Notifications\TaskAssigned($task));

# Check notifications
$user->notifications;
```

## 📊 Checking Notification Status

```php
// In Tinker or command
// Get user's unread notifications
User::find($userId)->notifications()->whereNull('read_at')->get();

// Mark notification as read
$notification = User::find($userId)->notifications()->first();
$notification->markAsRead();

// Check database
// SELECT * FROM notifications WHERE notifiable_id = $userId;
```

## 🐛 Common Issues & Fixes

| Issue | Solution |
|---|---|
| Emails not sending | Check SMTP config: `php artisan config:cache` |
| Gmail password rejected | Use App-Specific Password, quote if spaces |
| Queue jobs stuck | Run: `php artisan queue:work --timeout=60` |
| No emails in inbox | Check spam folder, verify MAIL_FROM_ADDRESS |
| Database notification not showing | Verify `notifications` table exists |

## 📁 File Locations

```
Email Templates:
  resources/views/emails/layouts/app.blade.php      # Master layout
  resources/views/emails/task-assigned.blade.php
  resources/views/emails/task-completed.blade.php
  resources/views/emails/leave-requested.blade.php
  resources/views/emails/leave-approved.blade.php
  resources/views/emails/leave-rejected.blade.php

Notification Classes:
  app/Notifications/TaskAssigned.php
  app/Notifications/TaskCompleted.php
  app/Notifications/LeaveRequested.php
  app/Notifications/LeaveApproved.php
  app/Notifications/LeaveRejected.php

Models:
  app/Models/NotificationPreference.php

Controllers:
  app/Http/Controllers/TestEmailController.php
  app/Http/Controllers/NotificationPreferenceController.php (future)
```

## 🔗 Integration Pattern

```php
// Step 1: Import notification
use App\Notifications\TaskAssigned;

// Step 2: Create/update resource
$task = EmployeeTask::create([...]);

// Step 3: Wrap in try-catch
try {
    $task->employee->user->notify(new TaskAssigned($task));
} catch (\Exception $e) {
    \Log::error('Notification failed: ' . $e->getMessage());
}

// Step 4: Return response
return redirect()->route('manager.tasks.index');
```

## 📧 Email Data Mapping

### Task Notification Data

Available in templates:
```php
$task           // EmployeeTask model
$employee       // Employee model
$manager        // Manager (Employee) model
$company        // Company model

// Access fields:
$task->title
$task->description
$task->priority
$task->due_date
$task->completed_at
$task->repository_url

$employee->user->name
$manager->user->name
$company->name
```

### Leave Notification Data

Available in templates:
```php
$leave          // Leave model
$manager        // User model

// Access fields:
$leave->type
$leave->start_date
$leave->end_date
$leave->reason
$leave->notes
$leave->status
$leave->employee->user->name
$leave->approvedBy->user->name
```

## 🚦 Queue Processing

### Start Queue Worker
```bash
# Simple (foreground)
php artisan queue:work

# With timeout and verbosity
php artisan queue:work --timeout=60 --verbose

# With max tries
php artisan queue:work --tries=3

# With specific queue
php artisan queue:work --queue=default
```

### Monitor Queue
```bash
# See failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Flush failed jobs
php artisan queue:flush
```

## ✨ Advanced Features (Future)

### User Preferences
```php
// Check if user wants notifications
$prefs = NotificationPreference::forUser($user);
if ($prefs->wantsEmail('task_assigned')) {
    // Send email
}
```

### Weekly Reports
```bash
php artisan schedule:work  # Local testing
php artisan make:command SendWeeklyReports  # Create command
```

### Activity Digest
```php
// Summarize activities for the week
$activities = ActivityLog::whereBetween('created_at', [
    now()->startOfWeek(),
    now()->endOfWeek()
])->get();
```

## 📞 Debug Commands

```bash
# Show all routes
php artisan route:list | grep email

# Check mail config
php artisan tinker
>>> config('mail.mailer')
>>> config('mail.from')

# Test SMTP
php artisan mail:test email@example.com

# Check migrations
php artisan migrate:status

# Clear all caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache
```

## 📚 Related Documentation

- **Main Guide:** [NOTIFICATION_SYSTEM.md](./NOTIFICATION_SYSTEM.md)
- **Laravel Mail:** https://laravel.com/docs/11.x/mail
- **Laravel Notifications:** https://laravel.com/docs/11.x/notifications
- **Laravel Queue:** https://laravel.com/docs/11.x/queues

---

**Last Updated:** 2026-05-08
**Version:** 1.0.0
