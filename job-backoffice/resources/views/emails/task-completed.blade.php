@component('emails.layouts.app', ['headerSubtitle' => 'Task Completion Notification'])
    <div class="greeting">Hello {{ $manager->user->name }},</div>

    <div class="description">
        <strong>{{ $employee->user->name }}</strong> has marked the following task as completed:
    </div>

    <div class="card success">
        <div class="info-row">
            <div class="info-label">Task:</div>
            <div class="info-value"><strong>{{ $task->title }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Employee:</div>
            <div class="info-value">{{ $employee->user->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value"><span class="badge success">✓ COMPLETED</span></div>
        </div>
        <div class="info-row">
            <div class="info-label">Completed At:</div>
            <div class="info-value">{{ $task->completed_at?->format('F d, Y - H:i A') ?? 'N/A' }}</div>
        </div>
        @if($task->priority)
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
        @endif
    </div>

    <div class="button-wrapper">
        <a href="{{ url('/manager/tasks/' . $task->id) }}" class="button success">Review Task</a>
    </div>

    <div class="description" style="font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: 20px;">
        You can review the task details, add comments, and mark it as verified in your manager dashboard.
    </div>

    <div class="timestamp">
        {{ now()->format('M d, Y g:i A') }}
    </div>
@endcomponent
