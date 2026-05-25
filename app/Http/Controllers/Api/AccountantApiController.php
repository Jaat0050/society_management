<?php

namespace App\Http\Controllers\Api;

use App\Models\Expense;
use App\Models\LedgerEntry;
use App\Models\MaintenanceBill;
use App\Models\Vendor;
use Illuminate\Http\Request;

class AccountantApiController extends Controller
{
    public function billing(Request $request)
    {
        return $this->ok(
            MaintenanceBill::where('society_id', $this->societyId())->with('flat')->latest()->paginate(30)
        );
    }

    public function expenses(Request $request)
    {
        return $this->ok(
            Expense::where('society_id', $this->societyId())->with('vendor')->latest('expense_date')->paginate(30)
        );
    }

    public function storeExpense(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);

        $expense = Expense::create([...$data, 'society_id' => $this->societyId()]);

        LedgerEntry::create([
            'society_id' => $this->societyId(),
            'type' => 'expense',
            'category' => $data['category'],
            'amount' => $data['amount'],
            'entry_date' => $data['expense_date'],
            'reference_type' => 'expense',
            'reference_id' => $expense->id,
            'description' => $data['description'],
        ]);

        return $this->ok($expense, 'Expense recorded', 201);
    }

    public function ledger(Request $request)
    {
        return $this->ok(
            LedgerEntry::where('society_id', $this->societyId())->orderByDesc('entry_date')->paginate(40)
        );
    }

    public function vendors(Request $request)
    {
        return $this->ok(Vendor::where('society_id', $this->societyId())->get());
    }

    public function reports(Request $request)
    {
        $sid = $this->societyId();

        return $this->ok([
            'income' => LedgerEntry::where('society_id', $sid)->where('type', 'income')->sum('amount'),
            'expense' => LedgerEntry::where('society_id', $sid)->where('type', 'expense')->sum('amount'),
            'pending_bills' => MaintenanceBill::where('society_id', $sid)->whereIn('status', ['pending', 'overdue'])->count(),
            'export_urls' => [
                'bills' => url('/admin/export/bills'),
                'ledger' => url('/admin/export/ledger'),
            ],
            'note' => 'Use society admin panel for full PDF export',
        ]);
    }
}
