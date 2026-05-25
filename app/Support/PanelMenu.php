<?php

namespace App\Support;

class PanelMenu
{
    public static function superAdmin(): array
    {
        return [
            ['route' => 'super-admin.dashboard', 'label' => 'Dashboard', 'icon' => '📊'],
            ['route' => 'super-admin.societies', 'label' => 'Societies', 'icon' => '🏢'],
            ['route' => 'super-admin.preview', 'label' => 'Society Module Tracker', 'icon' => '🔍'],
            ['route' => 'super-admin.admins', 'label' => 'Assign Admins', 'icon' => '👤'],
            ['route' => 'super-admin.plans', 'label' => 'Subscription Plans', 'icon' => '💳'],
            ['route' => 'super-admin.notifications', 'label' => 'Global Notifications', 'icon' => '📢'],
            ['route' => 'super-admin.reports', 'label' => 'Reports & Analytics', 'icon' => '📊'],
            ['route' => 'super-admin.settings', 'label' => 'App Settings', 'icon' => '⚙️'],
        ];
    }

    public static function societyAdmin(): array
    {
        return [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => '📊'],
            ['route' => 'admin.blocks', 'label' => 'Towers / Blocks', 'icon' => '🏗️'],
            ['route' => 'admin.flats', 'label' => 'Flats', 'icon' => '🏠'],
            ['route' => 'admin.residents', 'label' => 'Residents', 'icon' => '👥'],
            ['route' => 'admin.staff', 'label' => 'Staff', 'icon' => '🛡️'],
            ['route' => 'admin.staff-hub', 'label' => 'Attendance & Salary', 'icon' => '📋'],
            ['route' => 'admin.vendors', 'label' => 'Vendors', 'icon' => '🔧'],
            ['route' => 'admin.visitors', 'label' => 'Visitors', 'icon' => '🚪'],
            ['route' => 'admin.delivery-staff', 'label' => 'Delivery Staff', 'icon' => '📦'],
            ['route' => 'admin.complaints', 'label' => 'Complaints', 'icon' => '📋'],
            ['route' => 'admin.communications', 'label' => 'Communication', 'icon' => '📢'],
            ['route' => 'admin.notices', 'label' => 'Notices', 'icon' => '📌'],
            ['route' => 'admin.events', 'label' => 'Events', 'icon' => '📅'],
            ['route' => 'admin.billing', 'label' => 'Billing', 'icon' => '💰'],
            ['route' => 'admin.payments', 'label' => 'Payments', 'icon' => '✅'],
            ['route' => 'admin.expenses', 'label' => 'Expenses', 'icon' => '💸'],
            ['route' => 'admin.accounting', 'label' => 'Accounting', 'icon' => '📒'],
            ['route' => 'admin.parking', 'label' => 'Parking', 'icon' => '🚗'],
            ['route' => 'admin.amenities', 'label' => 'Amenities', 'icon' => '🏊'],
            ['route' => 'admin.security', 'label' => 'Security', 'icon' => '🔐'],
            ['route' => 'admin.settings', 'label' => 'Settings', 'icon' => '⚙️'],
            ['route' => 'admin.reports', 'label' => 'Reports & Analytics', 'icon' => '📊'],
        ];
    }

    public static function guard(): array
    {
        return [
            ['route' => 'guard.dashboard', 'label' => 'Gate Dashboard', 'icon' => '🏠'],
            ['route' => 'guard.new-entry', 'label' => 'New Entry', 'icon' => '➕'],
            ['route' => 'guard.scan', 'label' => 'Scan / Verify QR', 'icon' => '📱'],
            ['route' => 'guard.inside', 'label' => 'Inside Visitors', 'icon' => '🚶'],
            ['route' => 'guard.log', 'label' => 'Entry Log', 'icon' => '📋'],
        ];
    }

    public static function resident(): array
    {
        return [
            ['route' => 'resident.dashboard', 'label' => 'Home', 'icon' => '🏠'],
            ['route' => 'resident.bills', 'label' => 'My Bills', 'icon' => '💰'],
            ['route' => 'resident.complaints', 'label' => 'Complaints', 'icon' => '📋'],
            ['route' => 'resident.visitors', 'label' => 'Visitors', 'icon' => '🚪'],
            ['route' => 'resident.amenities', 'label' => 'Amenities', 'icon' => '🏊'],
            ['route' => 'resident.notices', 'label' => 'Notices', 'icon' => '📢'],
            ['route' => 'resident.sos', 'label' => 'Emergency SOS', 'icon' => '🆘'],
        ];
    }
}
