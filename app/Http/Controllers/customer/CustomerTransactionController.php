<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CustomerTransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = CustomerTransaction::with('customer');
        // Add search functionality
        if($request->filled('search')){
            $search = $request->search;
            $transactions->where(function($q) use ($search){
                $q->where('description', 'like', "%$search%")
                  ->orWhere('amount', 'like', "%$search%")
                  ->orWhereHas('customer', function($query) use ($search){
                      $query->where('name', 'like', "%$search%");
                  });
            });
        }
        
        // Add filter functionality
        if($request->filled('filter')){
            $filter = $request->filter;
            switch($filter){
                case 'sale':
                    $transactions->where('type', 'sale');
                    break;
                case 'return':
                    $transactions->where('type', 'return');
                    break;
                case 'payment':
                    $transactions->where('type', 'payment');
                    break;
            }
        }
        
        // Order by transaction_date if it exists, otherwise by created_at
        if (Schema::hasColumn('customer_transactions', 'transaction_date')) {
            $transactions = $transactions->latest('transaction_date')->get();
        } else {
            $transactions = $transactions->latest('created_at')->get();
        }
        
        // Calculate totals
        $totalSales = $transactions->where('type', 'sale')->sum('amount');
        $totalReturns = $transactions->where('type', 'return')->sum('amount');
        $totalPayments = $transactions->where('type', 'payment')->sum('amount');
        $balance = ($totalSales - $totalReturns) - $totalPayments;
        
        return view('customer.transactions.index', compact('transactions', 'totalSales', 'totalReturns', 'totalPayments', 'balance'));
    }
    
    public function create()
    {
        $customers = Customer::all();
        return view('customer.transactions.create', compact('customers'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'type' => 'required|in:sale,return,payment,adjustment',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'method' => 'nullable|in:cash,wallet,bank',
            'transaction_date' => 'required|date'
        ]);

        CustomerTransaction::create([
            'customer_id' => $request->customer_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
            'reference_type' => 'manual'
        ]);

        return redirect()->route('customer.transactions.index')->with('success', 'تم إضافة المعاملة بنجاح');
    }
}
