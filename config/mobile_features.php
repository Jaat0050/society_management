<?php

return [
    'payment_gateway_enabled' => false,
    'chat_poll_seconds' => 3,

    'roles' => [
        'resident' => [
            'profile', 'family', 'vehicles', 'documents',
            'bills', 'payment_history', 'complaints', 'visitors', 'gate_pass',
            'notices', 'events', 'amenities', 'sos', 'emergency_contacts',
            'chat', 'notifications', 'settings',
        ],
        'tenant' => [
            'profile', 'bills', 'payment_history', 'complaints', 'visitors', 'gate_pass',
            'notices', 'amenities', 'sos', 'chat', 'notifications', 'settings',
        ],
        'guard' => [
            'visitor_scan', 'visitor_checkin', 'visitor_checkout', 'visitor_log', 'gate_pass',
            'delivery_entry', 'sos_alerts', 'incidents', 'vehicle_verify', 'attendance',
            'chat', 'notifications', 'settings',
        ],
        'staff' => [
            'assigned_complaints', 'task_schedule', 'attendance', 'inventory_request',
            'chat', 'notifications', 'settings',
        ],
        'committee' => [
            'reports_summary', 'complaints_monitor', 'notices', 'events',
            'financial_summary', 'visitor_stats', 'chat', 'notifications', 'settings',
        ],
        'accountant' => [
            'billing', 'expenses', 'vendor_payments', 'ledger', 'reports_export',
            'chat', 'notifications', 'settings',
        ],
        'vendor' => [
            'assigned_tasks', 'invoices', 'schedule', 'payment_tracking',
            'chat', 'settings',
        ],
    ],
];
