<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use Illuminate\Http\Request;

class CustomerInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = CustomerInvoice::with('customer');
        
        // Add search functionality
        if($request->filled('search')){
            $search = $request->search;
            $invoices->where(function($q) use ($search){
                $q->where('invoice_number', 'like', "%$search%")
                  ->orWhereHas('customer', function($query) use ($search){
                      $query->where('name', 'like', "%$search%");
                  })
                  ->orWhere('total_amount', 'like', "%$search%")
                  ->orWhere('paid_amount', 'like', "%$search%")
                  ->orWhere('remining_amount', 'like', "%$search%")
                  ->orWhere('date', 'like', "%$search%");
            });
        }
        
        // Add filter functionality
        if($request->filled('filter')){
            $filter = $request->filter;
            switch($filter){
                case 'paid':
                    $invoices->where('state', 'paid');
                    break;
                case 'unpaid':
                    $invoices->where('state', 'unpaid');
                    break;
                case 'partial':
                    $invoices->where('state', 'partial');
                    break;
            }
        }
        
        $invoices = $invoices->latest('date')->get();
        
        return view('customer.invoices.index', compact('invoices'));
    }
}
