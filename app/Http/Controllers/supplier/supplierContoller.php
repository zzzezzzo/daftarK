<?php

namespace App\Http\Controllers\supplier;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Models\Supplier;
// use Illuminate\Console\View\Components\Alert;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;

class supplierContoller extends Controller
{
    public function index(Request $request){
        $suppliers = Supplier::query();
        if($request->filled('search')){
            $search = $request->search;
            $suppliers->where(function($q) use ($search){
                $q->where('name', 'like',"%$search%");
            });
        }
        $suppliers = $suppliers->latest()->paginate(5)->withQueryString();
        return view('supplier.index', compact('suppliers'));
    }
    public function create(){
        return view('supplier.create');
    }
    public function store(StoreSupplierRequest $request){
        Supplier::create($request->validated());
        Alert::success('نجاح' ,'تم اضافة مورد بنجاح');
        return redirect()->route('suppliers.index');
    }
    public function edit($id){
        $supplier = Supplier::findOrFail($id);
        return view('supplier.edit', compact('supplier'));
    }
    public function update(StoreSupplierRequest $request , $id){
        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->validated());
        Alert::success('نجاح', 'تم تعديل المورد بنجاح');
        return redirect()->route('suppliers.index');
    }
    public function destroy($id){
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        return redirect()->route('suppliers.index');
    }
}
