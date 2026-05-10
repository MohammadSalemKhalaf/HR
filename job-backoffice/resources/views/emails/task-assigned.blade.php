@component('emails.layouts.app', ['headerSubtitle' => 'New Task Assignment'])
    <div class="greeting">Hello {{ $employee->name }},</div>

    <div class="description">
        Your manager <strong>{{ $manager->user->name ?? 'Your Manager' }}</strong> has assigned you a new task
        from <strong>{{ $company->name ?? 'Karaaj' }}</strong>.
    </div>

    <div class="card info">
        <div class="info-row">
            <div class="info-label">Task:</div>
            <div class="info-value"><strong>{{ $task->title }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Priority:</div>
            <div class="info-value">
                @if($task->priority === 'high')
                    <span class="badge high">🔴 HIGH</span>
                @elseif($task->priority === 'medium')
                    <span class="badge medium">🟡 MEDIUM</span>
                @else
                    <span class="badge low">🟢 LOW</span>
                @endif
            </div>
        </div>
        @if($task->description)
            <div class="info-row">
                <div class="info-label">Details:</div>
                <div class="info-value">{{ $task->description }}</div>
            </div>
        @endif
        @if($task->due_date)
            <div class="info-row">
                <div class="info-label">Deadline:</div>
                <div class="info-value">{{ $task->due_date->format('F d, Y') }} ({{ $task->due_date->diffForHumans(now(), ['parts' => 1]) }})</div>
            </div>
        @endif
        @if($task->repository_url)
            <div class="info-row">
                <div class="info-label">Repository:</div>
                <div class="info-value"><a href="{{ $task->repository_url }}" style="color: #0284c7; text-decoration: none;">{{ parse_url($task->repository_url, PHP_URL_HOST) }}</a></div>
            </div>
        @endif
        <div class="info-row">
            <div class="info-label">Assigned By:</div>
            <div class="info-value">{{ $manager->user->name }}</div>
        </div>
    </div>

    <div class="button-wrapper">
        <a href="{{ url('/employee/tasks/' . $task->id) }}" class="button">Open My Tasks</a>
    </div>

    <div class="description" style="font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 20px;">
        Log in to your Karaaj HR-SaaS dashboard to view task details, track progress, and mark tasks as complete.
        You can also add notes and upload attachments directly in the task details page.
    </div>

    <div class="timestamp">
        {{ now()->format('M d, Y g:i A') }}
    </div>
@endcomponent
