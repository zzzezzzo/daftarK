<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchForCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;

class customerController extends Controller
{
    public function index(SearchForCustomerRequest $request){
        $customers = Customer::query();
        if($request->filled('search')){
            $search = $request->search;
            $customers->where(function($q)use ($search){
                $q->where('name', 'like',"%$search%");
            });
        }
        $customers = $customers->latest()->paginate(5)->withQueryString();
        return view('customer.index', compact('customers'));
    }
    public function create(){
        return view('customer.create');
    }
    public function store(StoreCustomerRequest $requset){
        Customer::create($requset->validated());
        Alert::success('نجاح' ,'تم اضافة عميل بنجاح');
        return redirect()->route('customer.index');
    }
    public function edit($id){
        $customer = Customer::findOrFail($id);
        return view('customer.edit', compact('customer'));
    }
    public function update(StoreCustomerRequest $request ,$id){
        $customer = Customer::findOrFail($id);
        $customer->update($request->validated());
        Alert::success('نجاح', 'تم تعديل العميل بنجاح');
        return redirect()->route('customer.index');
    }
    public function destroy($id){
        $customer = Customer::findOrFail($id);
        $customer->delete();
        return redirect()->route('customer.index');
    }
}
