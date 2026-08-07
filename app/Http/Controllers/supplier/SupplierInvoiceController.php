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
    public function listForImport(Request $request)
{
    $query = SupplierInvoice::with('supplier')
        ->orderByDesc('date')
        ->orderByDesc('id');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhereHas('supplier', function ($sq) use ($search) {
                  $sq->where('name', 'like', "%{$search}%");
              });
        });
    }

    $invoices = $query->limit(30)->get()->map(function ($invoice) {
        return [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'supplier_name' => $invoice->supplier->name ?? '-',
            'date' => optional($invoice->date)->format('Y-m-d'),
            'total_amount' => $invoice->total_amount,
            'items_count' => $invoice->items()->count(),
        ];
    });

    return response()->json($invoices);
}

public function getItemsForImport(SupplierInvoice $supplierInvoice)
{
    $items = $supplierInvoice->items()->with('product')->get()->map(function ($item) {
        return [
            'product_id' => $item->product_id,
            'product_name' => $item->product->name ?? 'منتج محذوف',
            'quantity' => $item->quantity,
            // سعر الشراء بيرجع للمرجعية فقط، مش هنستخدمه في فاتورة البيع
            'purchase_price' => $item->price,
        ];
    });

    return response()->json([
        'invoice_number' => $supplierInvoice->invoice_number,
        'items' => $items,
    ]);
}
}
