<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-500">Manager Area</p>
                <h2 class="text-3xl font-bold text-slate-900">Department Notifications</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm">
                <div class="font-semibold">Please fix the highlighted errors.</div>
                <ul class="mt-2 list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-slate-900">Send Department Notification</h3>
                    <p class="mt-1 text-sm text-slate-500">Broadcast to the entire department or select specific employees.</p>
                </div>

                <form method="POST" action="{{ route('manager.department-notifications.store') }}" id="department-notification-form" class="space-y-6">
                    @csrf

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label for="department_id" class="mb-2 block text-sm font-medium text-slate-700">Department</label>
                            <select id="department_id" name="department_id" class="w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" @selected(old('department_id', $selectedDepartmentId) === $department->id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="type" class="mb-2 block text-sm font-medium text-slate-700">Notification Type</label>
                            <select id="type" name="type" class="w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                                @foreach($notificationTypes as $key => $label)
                                    <option value="{{ $key }}" @selected(old('type', 'general_announcement') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Title</label>
                        <input id="title" name="title" type="text" value="{{ old('title') }}" placeholder="Meeting tomorrow at 10 AM" class="w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-sm focus:border-cyan-500 focus:ring-cyan-500" />
                    </div>

                    <div>
                        <label for="message" class="mb-2 block text-sm font-medium text-slate-700">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Write the full message that employees should receive." class="w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-sm focus:border-cyan-500 focus:ring-cyan-500">{{ old('message') }}</textarea>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="mb-3 text-sm font-semibold text-slate-800">Recipients</div>
                        <div class="flex flex-wrap gap-4 text-sm">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="recipient_mode" value="all" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked(old('recipient_mode', 'all') === 'all')>
                                <span>Send to all employees</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="recipient_mode" value="specific" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500" @checked(old('recipient_mode') === 'specific')>
                                <span>Select specific employees</span>
                            </label>
                        </div>
                    </div>

                    <div id="specific-employees-box" class="rounded-2xl border border-slate-200 bg-white p-4">
                        <div class="mb-3 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-slate-800">Employees in Department</div>
                                <div class="text-xs text-slate-500">Use checkboxes to target individual employees.</div>
                            </div>
                            <div class="text-xs text-slate-400" id="employee-count-label">0 employees</div>
                        </div>
                        <div id="employee-checkboxes" class="grid gap-3 md:grid-cols-2"></div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-700">Send Notification</button>
                        <a href="{{ route('manager.department-notifications.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
                    </div>

                    <div class="text-xs text-slate-500">
                        Employees receive both database notifications and email notifications.
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Selected Department</h3>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <div><span class="font-medium text-slate-900">Department:</span> {{ $selectedDepartment?->name ?? 'N/A' }}</div>
                        <div><span class="font-medium text-slate-900">Manager Scope:</span> Your own department employees only</div>
                        <div><span class="font-medium text-slate-900">Delivery:</span> Database + Email</div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Current Employees</h3>
                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        @forelse(($departmentEmployees[$selectedDepartmentId] ?? collect()) as $employee)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $employee['name'] }}</div>
                                <div class="text-xs text-slate-500">{{ $employee['email'] }}</div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-slate-500">No employees found in this department.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const departments = @json($departments->map(fn ($department) => ['id' => $department->id, 'name' => $department->name]));
            const employeesByDepartment = @json($departmentEmployees);
            const departmentSelect = document.getElementById('department_id');
            const recipientModeInputs = document.querySelectorAll('input[name="recipient_mode"]');
            const employeeBox = document.getElementById('specific-employees-box');
            const employeeContainer = document.getElementById('employee-checkboxes');
            const employeeCountLabel = document.getElementById('employee-count-label');

            const renderEmployees = () => {
                const departmentId = departmentSelect.value;
                const employees = employeesByDepartment[departmentId] || [];
                employeeContainer.innerHTML = '';
                employeeCountLabel.textContent = `${employees.length} employee${employees.length === 1 ? '' : 's'}`;

                if (!employees.length) {
                    employeeContainer.innerHTML = '<div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500 md:col-span-2">No employees are available for this department.</div>';
                    return;
                }

                employees.forEach((employee) => {
                    const item = document.createElement('label');
                    item.className = 'flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700';
                    item.innerHTML = `
                        <input type="checkbox" name="employee_ids[]" value="${employee.id}" class="mt-1 rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                        <div>
                            <div class="font-semibold text-slate-900">${employee.name}</div>
                            <div class="text-xs text-slate-500">${employee.email ?? ''}</div>
                        </div>
                    `;
                    employeeContainer.appendChild(item);
                });
            };

            const toggleEmployeeBox = () => {
                const selectedMode = document.querySelector('input[name="recipient_mode"]:checked')?.value || 'all';
                employeeBox.classList.toggle('hidden', selectedMode !== 'specific');
            };

            departmentSelect.addEventListener('change', renderEmployees);
            recipientModeInputs.forEach((input) => input.addEventListener('change', toggleEmployeeBox));

            renderEmployees();
            toggleEmployeeBox();
        });
    </script>
</x-app-layout>
