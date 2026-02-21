<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category');
        if($request->filled('search')){
            $search = $request->search;
            $products->where(function($q) use ($search){
                $q->where('name', 'like',"%$search%")
                ->orwhere('code', 'like', "%$search%");
            });
        }
        $products =$products->latest()->paginate(5)->withQueryString();
        $outofstockProduct = Product::where('stock', 0)->get();
        return view('product.index', compact('products', 'outofstockProduct'));
    }
    
    public function create()
    {
        $categories = Category::all();
        return view('product.create', compact('categories'));
    }
    
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();
        // Calculate trade and technical prices based on category rates if not provided
        $category = Category::with('priceRate')->find($validatedData['category_id']);
        $basePrice = $validatedData['price_base'];
        if ($category && $category->priceRate) {
            // الحالة 1: الحساب بناءً على نسب القسم
            $validatedData['price_trade']      = $basePrice + ($basePrice * ($category->priceRate->rate_trade / 100));
            $validatedData['price_technician'] = $basePrice + ($basePrice * ($category->priceRate->rate_technician / 100));
            $validatedData['price_customer']   = $basePrice + ($basePrice * ($category->priceRate->rate_client / 100));
        } else {
            // الحالة 2: لو مفيش نسب للقسم، حط السعر الأساسي كقيمة افتراضية عشان الداتا بيز متضربش
            $validatedData['price_trade']      = $basePrice;
            $validatedData['price_technician'] = $basePrice;
            $validatedData['price_customer']   = $basePrice;
        }
        
        Product::create($validatedData);
        Alert::success('نجاح', 'تم اضافة المنتج بنجاح');
        return redirect()->route('products.index')->with('success', 'تم إضافة المنتج بنجاح.');
    }
    
    public function edit($id)
    {
        $product = Product::with('category')->findOrFail($id);
        $categories = Category::all();
        return view('product.edit', compact('product', 'categories'));
    }
    
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $validatedData = $request->validated();
        
        // Calculate trade and technical prices based on category rates if not provided
        if (!isset($validatedData['price_trade']) || $validatedData['price_trade'] === null) {
            $category = Category::find($validatedData['category_id']);
            if ($category && $category->priceRate) {
                $basePrice = $validatedData['price_base'] ?? $product->price_base;
                $validatedData['price_trade'] = $basePrice + $basePrice * ($category->priceRate->rate_trade / 100);
                $validatedData['price_technician'] = $basePrice + $basePrice * ($category->priceRate->rate_technician / 100);
                $validatedData['price_customer'] = $basePrice + $basePrice * ($category->priceRate->rate_customer / 100);
            }
        }
        
        $product->update($validatedData);
        Alert::success('نجاح', 'تم تعديل المنتج بنجاح');
        return redirect()->route('products.index');
    }
    
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'تم حذف المنتج بنجاح.');
    }
}
