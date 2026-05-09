<?php

return [
    'attendance' => [
        'workday_start' => env('EMS_WORKDAY_START', '09:00'),
        'workday_end' => env('EMS_WORKDAY_END', '17:00'),
    ],
    'department_notifications' => [
        'types' => [
            'general_announcement' => 'General Announcement',
            'meeting' => 'Meeting',
            'warning' => 'Warning',
            'delay_notice' => 'Delay Notice',
            'salary_deduction' => 'Salary Deduction',
            'performance_notice' => 'Performance Notice',
        ],
    ],
];
