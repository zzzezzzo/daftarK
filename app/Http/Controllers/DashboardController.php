<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $productlegth = Product::count();
        $supplierlegth = Supplier::count();
        $customerlength = Customer::count();
        $invoicelegth = CustomerInvoice::count();
        return view('dashboard', compact('productlegth','supplierlegth','customerlength','invoicelegth'));
    }
}
