<?php

namespace App\Http\Controllers\supplier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SupplierInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = SupplierInvoice::with('supplier');
        
        // Add search functionality
        if($request->filled('search')){
            $search = $request->search;
            $invoices->where(function($q) use ($search){
                $q->where('invoice_number', 'like', "%$search%")
                  ->orWhereHas('supplier', function($query) use ($search){
                      $query->where('name', 'like', "%$search%");
                  })
                  ->orWhere('total_amount', 'like', "%$search%")
                  ->orWhere('paid_amount', 'like', "%$search%")
                  ->orWhere('Remaining_amount', 'like', "%$search%")
                  ->orWhere('date', 'like', "%$search%");
            });
        }
        
        // Add filter functionality
        if($request->filled('filter')){
            $filter = $request->filter;
            switch($filter){
                case 'paid':
                    $invoices->where('states', 'paid');
                    break;
                case 'unpaid':
                    $invoices->where('states', 'unpaid');
                    break;
                case 'partially_paid':
                    $invoices->where('states', 'partially_paid');
                    break;
            }
        }
        
        $invoices = $invoices->latest('date')->get();
        
        return view('supplier.invoices.index', compact('invoices'));
    }
}
