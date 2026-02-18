<?php

namespace App\Http\Controllers\supplier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SupplierTransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = SupplierTransaction::with('supplier');
        
        // Add search functionality
        if($request->filled('search')){
            $search = $request->search;
            $transactions->where(function($q) use ($search){
                $q->where('description', 'like', "%$search%")
                  ->orWhere('amount', 'like', "%$search%")
                  ->orWhereHas('supplier', function($query) use ($search){
                      $query->where('name', 'like', "%$search%");
                  });
            });
        }
        
        // Add filter functionality
        if($request->filled('filter')){
            $filter = $request->filter;
            switch($filter){
                case 'deposit':
                    $transactions->where('type', 'deposit');
                    break;
                case 'withdrawal':
                    $transactions->where('type', 'withdrawal');
                    break;
            }
        }
        
        // Order by transaction_date if it exists, otherwise by created_at
        if (Schema::hasColumn('supplier_transactions', 'transaction_date')) {
            $transactions = $transactions->latest('transaction_date')->get();
        } else {
            $transactions = $transactions->latest('created_at')->get();
        }
        
        // Calculate totals
        $totalDeposits = $transactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $transactions->where('type', 'withdrawal')->sum('amount');
        $balance = $totalDeposits - $totalWithdrawals;
        
        return view('supplier.transactions.index', compact('transactions', 'totalDeposits', 'totalWithdrawals', 'balance'));
    }
    
    public function create()
    {
        $suppliers = Supplier::all();
        return view('supplier.transactions.create', compact('suppliers'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'type' => 'required|in:deposit,withdrawal',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date'
        ]);

        // Create basic transaction with only existing columns
        $transaction = new SupplierTransaction();
        $transaction->supplier_id = $request->supplier_id;
        $transaction->type = $request->type;
        $transaction->amount = $request->amount;
        
        // Save basic transaction first
        $transaction->save();
        
        // Try to add additional fields if columns exist
        try {
            if (Schema::hasColumn('supplier_transactions', 'description')) {
                $transaction->description = $request->description;
            }
            
            if (Schema::hasColumn('supplier_transactions', 'transaction_date')) {
                $transaction->transaction_date = $request->transaction_date;
            }
            
            if (Schema::hasColumn('supplier_transactions', 'reference_id')) {
                $transaction->reference_id = null;
            }
            
            if (Schema::hasColumn('supplier_transactions', 'reference_type')) {
                $transaction->reference_type = 'manual';
            }
            
            $transaction->save();
        } catch (\Exception $e) {
            // If additional fields fail, at least the basic transaction is saved
            \Log::warning('Could not save additional supplier transaction fields: ' . $e->getMessage());
        }

        return redirect()->route('supplier.transactions.index')->with('success', 'تم إضافة معاملة المورد بنجاح');
    }
}
