<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use App\Models\Event;
use App\Models\Expense;
use App\Models\MaintenanceBill;
use App\Models\Notice;
use App\Models\VisitorEntry;
use Illuminate\Http\Request;

class CommitteeApiController extends Controller
{
    public function overview(Request $request)
    {
        $sid = $this->societyId();

        return $this->ok([
            'collected' => MaintenanceBill::where('society_id', $sid)->where('status', 'paid')->sum('paid_amount'),
            'pending' => MaintenanceBill::where('society_id', $sid)->whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'expenses' => Expense::where('society_id', $sid)->sum('amount'),
            'open_complaints' => Complaint::where('society_id', $sid)->whereIn('status', ['open', 'in_progress'])->count(),
            'visitors_today' => VisitorEntry::where('society_id', $sid)->whereDate('created_at', today())->count(),
        ]);
    }

    public function complaints(Request $request)
    {
        return $this->ok(
            Complaint::where('society_id', $this->societyId())->with(['flat', 'assignedStaff'])->latest()->paginate(20)
        );
    }

    public function notices(Request $request)
    {
        return $this->ok(Notice::where('society_id', $this->societyId())->latest()->paginate(20));
    }

    public function events(Request $request)
    {
        return $this->ok(Event::where('society_id', $this->societyId())->orderBy('starts_at')->get());
    }

    public function visitorStats(Request $request)
    {
        $sid = $this->societyId();

        return $this->ok([
            'today' => VisitorEntry::where('society_id', $sid)->whereDate('created_at', today())->count(),
            'inside' => VisitorEntry::where('society_id', $sid)->where('status', 'checked_in')->count(),
            'pending' => VisitorEntry::where('society_id', $sid)->where('status', 'pending')->count(),
            'week' => VisitorEntry::where('society_id', $sid)->where('created_at', '>=', now()->subDays(7))->count(),
        ]);
    }

    public function financialSummary(Request $request)
    {
        $sid = $this->societyId();

        return $this->ok([
            'collected' => MaintenanceBill::where('society_id', $sid)->where('status', 'paid')->sum('paid_amount'),
            'pending' => MaintenanceBill::where('society_id', $sid)->whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'expenses' => Expense::where('society_id', $sid)->sum('amount'),
            'by_status' => MaintenanceBill::where('society_id', $sid)->selectRaw('status, count(*) as cnt')->groupBy('status')->pluck('cnt', 'status'),
        ]);
    }
}
