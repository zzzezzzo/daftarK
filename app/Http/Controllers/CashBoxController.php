<?php

namespace App\Http\Controllers;

use App\Models\CashBox;
use App\Models\CashBoxTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class CashBoxController extends Controller
{
    public function index()
    {
        $cashBoxes = CashBox::with(['user', 'transactions' => function($query) {
            $query->latest()->limit(10);
        }])->get();

        return view('cashBox.index', compact('cashBoxes'));
    }

    public function create()
    {
        return view('cashBox.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'opening_balance' => 'required|numeric|min:0'
        ]);

        DB::transaction(function() use ($request) {
            $cashBox = CashBox::create([
                'name' => $request->name,
                'description' => $request->description,
                'opening_balance' => $request->opening_balance,
                'current_balance' => $request->opening_balance,
                'total_in' => $request->opening_balance,
                'total_out' => 0,
                'status' => 'active',
                'user_id' => auth()->id()
            ]);

            // Create opening transaction
            CashBoxTransaction::create([
                'cash_box_id' => $cashBox->id,
                'type' => 'in',
                'amount' => $request->opening_balance,
                'description' => 'رصيد افتتاحي',
                'reference_type' => 'opening',
                'user_id' => auth()->id()
            ]);
        });

        Alert::success('نجاح', 'تم إنشاء الصندوق بنجاح');
        return redirect()->route('cashBoxes.index');
    }

    public function show(CashBox $cashBox)
    {
        $transactions = $cashBox->transactions()
            ->with(['user'])
            ->latest()
            ->paginate(50);

        return view('cashBox.show', compact('cashBox', 'transactions'));
    }

    public function addTransaction(Request $request, CashBox $cashBox)
    {
        $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255'
        ]);

        DB::transaction(function() use ($request, $cashBox) {
            // Create transaction
            CashBoxTransaction::create([
                'cash_box_id' => $cashBox->id,
                'type' => $request->type,
                'amount' => $request->amount,
                'description' => $request->description,
                'reference_type' => 'manual',
                'user_id' => auth()->id()
            ]);

            // Update cash box balance
            if ($request->type === 'in') {
                $cashBox->increment('current_balance', $request->amount);
                $cashBox->increment('total_in', $request->amount);
            } else {
                $cashBox->decrement('current_balance', $request->amount);
                $cashBox->increment('total_out', $request->amount);
            }
        });

        Alert::success('نجاح', 'تم إضافة المعاملة بنجاح');
        return redirect()->route('cashBoxes.show', $cashBox->id);
    }

    public function close(CashBox $cashBox)
    {
        $cashBox->update(['status' => 'closed']);
        Alert::success('نجاح', 'تم إغلاق الصندوق بنجاح');
        return redirect()->route('cashBoxes.index');
    }

    public function reopen(CashBox $cashBox)
    {
        $cashBox->update(['status' => 'active']);
        
        Alert::success('نجاح', 'تم إعادة فتح الصندوق بنجاح');
        return redirect()->route('cashBoxes.show', $cashBox->id);
    }

    public function report(CashBox $cashBox)
    {
        $transactions = $cashBox->transactions()
            ->with(['user'])
            ->latest()
            ->get();

        $summary = [
            'opening_balance' => $cashBox->opening_balance,
            'total_in' => $cashBox->total_in,
            'total_out' => $cashBox->total_out,
            'current_balance' => $cashBox->current_balance,
            'transaction_count' => $transactions->count()
        ];

        return view('cashBox.report', compact('cashBox', 'transactions', 'summary'));
    }
}
