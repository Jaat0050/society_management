<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Expense;
use App\Models\LedgerEntry;
use App\Models\MaintenanceBill;
use App\Models\Member;
use App\Models\VisitorEntry;
use App\Services\ExportService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function __construct(private ExportService $export) {}

    public function bills(Request $request)
    {
        $sid = auth()->user()->society_id;
        $rows = MaintenanceBill::where('society_id', $sid)->with('flat')->get()
            ->map(fn ($b) => [
                $b->bill_month, $b->flat?->label, $b->title, $b->amount, $b->penalty_amount,
                $b->status, $b->due_date?->format('Y-m-d'), $b->paid_at?->format('Y-m-d'),
            ]);

        return $this->export->csvDownload('maintenance-bills.csv',
            ['Month', 'Flat', 'Title', 'Amount', 'Penalty', 'Status', 'Due', 'Paid'],
            $rows
        );
    }

    public function residents(Request $request)
    {
        $sid = auth()->user()->society_id;
        $rows = Member::where('society_id', $sid)->with('flat')->get()
            ->map(fn ($m) => [
                $m->name, $m->phone, $m->email, $m->member_type, $m->flat?->label,
                $m->move_in_date?->format('Y-m-d'),
            ]);

        return $this->export->csvDownload('residents.csv',
            ['Name', 'Phone', 'Email', 'Type', 'Flat', 'Move In'],
            $rows
        );
    }

    public function ledger(Request $request)
    {
        $sid = auth()->user()->society_id;
        $rows = LedgerEntry::where('society_id', $sid)->orderByDesc('entry_date')->get()
            ->map(fn ($e) => [
                $e->entry_date->format('Y-m-d'), $e->type, $e->category, $e->amount, $e->description,
            ]);

        return $this->export->csvDownload('ledger.csv',
            ['Date', 'Type', 'Category', 'Amount', 'Description'],
            $rows
        );
    }

    public function visitors(Request $request)
    {
        $sid = auth()->user()->society_id;
        $rows = VisitorEntry::where('society_id', $sid)->with('flat')->latest()->get()
            ->map(fn ($v) => [
                $v->visitor_name, $v->pass_code, $v->visit_type, $v->flat?->label,
                $v->status, $v->entry_at?->format('Y-m-d H:i'), $v->exit_at?->format('Y-m-d H:i'),
            ]);

        return $this->export->csvDownload('visitors.csv',
            ['Visitor', 'Pass', 'Type', 'Flat', 'Status', 'Entry', 'Exit'],
            $rows
        );
    }
}
