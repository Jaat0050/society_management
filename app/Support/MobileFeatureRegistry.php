<?php

namespace App\Support;

class MobileFeatureRegistry
{
    public static function isMobileRole(string $role): bool
    {
        return in_array($role, array_keys(config('mobile_features.roles', [])), true);
    }

    public static function featuresFor(string $role): array
    {
        return config("mobile_features.roles.{$role}", []);
    }

    public static function menusFor(string $role): array
    {
        $all = self::allMenuDefinitions();
        $features = self::featuresFor($role);
        $roleMenus = $all[$role] ?? $all['resident'];

        return array_values(array_filter($roleMenus, function ($m) use ($features) {
            $req = $m['feature'] ?? null;
            if ($req === null) {
                return true;
            }

            return in_array($req, $features, true);
        }));
    }

    protected static function allMenuDefinitions(): array
    {
        return [
            'resident' => [
                ['id' => 'home', 'title' => 'Home', 'icon' => 'home', 'route' => '/home', 'feature' => null],
                ['id' => 'profile', 'title' => 'Profile', 'icon' => 'person', 'route' => '/profile', 'feature' => 'profile'],
                ['id' => 'family', 'title' => 'Family', 'icon' => 'family', 'route' => '/family', 'feature' => 'family'],
                ['id' => 'vehicles', 'title' => 'Vehicles', 'icon' => 'car', 'route' => '/vehicles', 'feature' => 'vehicles'],
                ['id' => 'documents', 'title' => 'Documents', 'icon' => 'folder', 'route' => '/documents', 'feature' => 'documents'],
                ['id' => 'bills', 'title' => 'Bills', 'icon' => 'receipt', 'route' => '/bills', 'feature' => 'bills'],
                ['id' => 'payments', 'title' => 'Payments', 'icon' => 'payment', 'route' => '/payments', 'feature' => 'payment_history'],
                ['id' => 'complaints', 'title' => 'Complaints', 'icon' => 'report', 'route' => '/complaints', 'feature' => 'complaints'],
                ['id' => 'visitors', 'title' => 'Visitors', 'icon' => 'people', 'route' => '/visitors', 'feature' => 'visitors'],
                ['id' => 'gate_pass', 'title' => 'Gate Pass', 'icon' => 'qr_code', 'route' => '/gate-pass', 'feature' => 'gate_pass'],
                ['id' => 'chat', 'title' => 'Community Chat', 'icon' => 'chat', 'route' => '/chat', 'feature' => 'chat'],
                ['id' => 'notices', 'title' => 'Notices', 'icon' => 'campaign', 'route' => '/notices', 'feature' => 'notices'],
                ['id' => 'events', 'title' => 'Events', 'icon' => 'event', 'route' => '/events', 'feature' => 'events'],
                ['id' => 'amenities', 'title' => 'Amenities', 'icon' => 'pool', 'route' => '/amenities', 'feature' => 'amenities'],
                ['id' => 'bookings', 'title' => 'Bookings', 'icon' => 'book', 'route' => '/bookings', 'feature' => 'amenities'],
                ['id' => 'sos', 'title' => 'SOS', 'icon' => 'emergency', 'route' => '/sos', 'feature' => 'sos'],
                ['id' => 'emergency', 'title' => 'Emergency', 'icon' => 'phone', 'route' => '/emergency', 'feature' => 'emergency_contacts'],
                ['id' => 'notifications', 'title' => 'Alerts', 'icon' => 'notifications', 'route' => '/notifications', 'feature' => 'notifications'],
            ],
            'tenant' => [
                ['id' => 'home', 'title' => 'Home', 'icon' => 'home', 'route' => '/home', 'feature' => null],
                ['id' => 'profile', 'title' => 'Profile', 'icon' => 'person', 'route' => '/profile', 'feature' => 'profile'],
                ['id' => 'bills', 'title' => 'Bills', 'icon' => 'receipt', 'route' => '/bills', 'feature' => 'bills'],
                ['id' => 'payments', 'title' => 'Payments', 'icon' => 'payment', 'route' => '/payments', 'feature' => 'payment_history'],
                ['id' => 'complaints', 'title' => 'Complaints', 'icon' => 'report', 'route' => '/complaints', 'feature' => 'complaints'],
                ['id' => 'visitors', 'title' => 'Visitors', 'icon' => 'people', 'route' => '/visitors', 'feature' => 'visitors'],
                ['id' => 'gate_pass', 'title' => 'Gate Pass', 'icon' => 'qr_code', 'route' => '/gate-pass', 'feature' => 'gate_pass'],
                ['id' => 'chat', 'title' => 'Community Chat', 'icon' => 'chat', 'route' => '/chat', 'feature' => 'chat'],
                ['id' => 'notices', 'title' => 'Notices', 'icon' => 'campaign', 'route' => '/notices', 'feature' => 'notices'],
                ['id' => 'amenities', 'title' => 'Amenities', 'icon' => 'pool', 'route' => '/amenities', 'feature' => 'amenities'],
                ['id' => 'bookings', 'title' => 'Bookings', 'icon' => 'book', 'route' => '/bookings', 'feature' => 'amenities'],
                ['id' => 'sos', 'title' => 'SOS', 'icon' => 'emergency', 'route' => '/sos', 'feature' => 'sos'],
                ['id' => 'chat', 'title' => 'Community Chat', 'icon' => 'chat', 'route' => '/chat', 'feature' => 'chat'],
                ['id' => 'notifications', 'title' => 'Alerts', 'icon' => 'notifications', 'route' => '/notifications', 'feature' => 'notifications'],
            ],
            'guard' => [
                ['id' => 'home', 'title' => 'Gate', 'icon' => 'security', 'route' => '/home', 'feature' => null],
                ['id' => 'scan', 'title' => 'Scan QR/OTP', 'icon' => 'qr_code', 'route' => '/scan', 'feature' => 'visitor_scan'],
                ['id' => 'new_entry', 'title' => 'New Entry', 'icon' => 'add', 'route' => '/new-entry', 'feature' => 'visitor_checkin'],
                ['id' => 'inside', 'title' => 'Inside', 'icon' => 'groups', 'route' => '/inside', 'feature' => 'visitor_checkin'],
                ['id' => 'checkin', 'title' => 'Check-in', 'icon' => 'login', 'route' => '/checkin', 'feature' => 'visitor_checkin'],
                ['id' => 'checkout', 'title' => 'Check-out', 'icon' => 'logout', 'route' => '/checkout', 'feature' => 'visitor_checkout'],
                ['id' => 'log', 'title' => 'Visitor Log', 'icon' => 'list', 'route' => '/log', 'feature' => 'visitor_log'],
                ['id' => 'delivery', 'title' => 'Delivery', 'icon' => 'delivery', 'route' => '/delivery', 'feature' => 'delivery_entry'],
                ['id' => 'vehicle', 'title' => 'Vehicle', 'icon' => 'car', 'route' => '/vehicle', 'feature' => 'vehicle_verify'],
                ['id' => 'attendance', 'title' => 'Attendance', 'icon' => 'check', 'route' => '/attendance', 'feature' => 'attendance'],
                ['id' => 'staff', 'title' => 'Staff', 'icon' => 'badge', 'route' => '/staff-list', 'feature' => 'attendance'],
                ['id' => 'sos', 'title' => 'SOS Alerts', 'icon' => 'emergency', 'route' => '/sos-alerts', 'feature' => 'sos_alerts'],
                ['id' => 'incidents', 'title' => 'Incidents', 'icon' => 'warning', 'route' => '/incidents', 'feature' => 'incidents'],
                ['id' => 'gate_pass', 'title' => 'Gate Pass', 'icon' => 'qr_code', 'route' => '/gate-pass', 'feature' => 'gate_pass'],
                ['id' => 'chat', 'title' => 'Gate Chat', 'icon' => 'chat', 'route' => '/chat', 'feature' => 'chat'],
            ],
            'staff' => [
                ['id' => 'tasks', 'title' => 'Tasks', 'icon' => 'build', 'route' => '/tasks', 'feature' => 'assigned_complaints'],
                ['id' => 'schedule', 'title' => 'Schedule', 'icon' => 'calendar', 'route' => '/schedule', 'feature' => 'task_schedule'],
                ['id' => 'attendance', 'title' => 'Attendance', 'icon' => 'check', 'route' => '/attendance', 'feature' => 'attendance'],
                ['id' => 'inventory', 'title' => 'Inventory', 'icon' => 'inventory', 'route' => '/inventory', 'feature' => 'inventory_request'],
                ['id' => 'chat', 'title' => 'Team Chat', 'icon' => 'chat', 'route' => '/chat', 'feature' => 'chat'],
                ['id' => 'notifications', 'title' => 'Alerts', 'icon' => 'notifications', 'route' => '/notifications', 'feature' => 'notifications'],
            ],
            'committee' => [
                ['id' => 'home', 'title' => 'Overview', 'icon' => 'dashboard', 'route' => '/home', 'feature' => null],
                ['id' => 'complaints', 'title' => 'Complaints', 'icon' => 'report', 'route' => '/complaints', 'feature' => 'complaints_monitor'],
                ['id' => 'notices', 'title' => 'Notices', 'icon' => 'campaign', 'route' => '/notices', 'feature' => 'notices'],
                ['id' => 'events', 'title' => 'Events', 'icon' => 'event', 'route' => '/events', 'feature' => 'events'],
                ['id' => 'financial', 'title' => 'Finance', 'icon' => 'money', 'route' => '/financial', 'feature' => 'financial_summary'],
                ['id' => 'reports', 'title' => 'Reports', 'icon' => 'analytics', 'route' => '/reports', 'feature' => 'reports_summary'],
                ['id' => 'visitors', 'title' => 'Visitors', 'icon' => 'people', 'route' => '/visitor-stats', 'feature' => 'visitor_stats'],
                ['id' => 'chat', 'title' => 'Society Chat', 'icon' => 'chat', 'route' => '/chat', 'feature' => 'chat'],
                ['id' => 'notifications', 'title' => 'Alerts', 'icon' => 'notifications', 'route' => '/notifications', 'feature' => 'notifications'],
            ],
            'accountant' => [
                ['id' => 'billing', 'title' => 'Billing', 'icon' => 'receipt', 'route' => '/billing', 'feature' => 'billing'],
                ['id' => 'expenses', 'title' => 'Expenses', 'icon' => 'money', 'route' => '/expenses', 'feature' => 'expenses'],
                ['id' => 'ledger', 'title' => 'Ledger', 'icon' => 'book', 'route' => '/ledger', 'feature' => 'ledger'],
                ['id' => 'vendors', 'title' => 'Vendors', 'icon' => 'store', 'route' => '/vendors', 'feature' => 'vendor_payments'],
                ['id' => 'reports', 'title' => 'Reports', 'icon' => 'analytics', 'route' => '/reports', 'feature' => 'reports_export'],
                ['id' => 'chat', 'title' => 'Society Chat', 'icon' => 'chat', 'route' => '/chat', 'feature' => 'chat'],
                ['id' => 'notifications', 'title' => 'Alerts', 'icon' => 'notifications', 'route' => '/notifications', 'feature' => 'notifications'],
            ],
            'vendor' => [
                ['id' => 'tasks', 'title' => 'Tasks', 'icon' => 'work', 'route' => '/tasks', 'feature' => 'assigned_tasks'],
                ['id' => 'invoices', 'title' => 'Invoices', 'icon' => 'description', 'route' => '/invoices', 'feature' => 'invoices'],
                ['id' => 'schedule', 'title' => 'Schedule', 'icon' => 'calendar', 'route' => '/schedule', 'feature' => 'schedule'],
                ['id' => 'payments', 'title' => 'Payments', 'icon' => 'payment', 'route' => '/payments', 'feature' => 'payment_tracking'],
                ['id' => 'chat', 'title' => 'Society Chat', 'icon' => 'chat', 'route' => '/chat', 'feature' => 'chat'],
            ],
        ];
    }
}
