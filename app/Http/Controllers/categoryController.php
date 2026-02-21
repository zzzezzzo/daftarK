<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('priceRate')->paginate();
        return view('category.index', compact('categories'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ]);

        $category = Category::create([
            'name' => $request->name
        ]);

        // Return JSON for AJAX handling
        return response()->json($category);
    }
    
    public function edit($id)
    {
        $category = Category::with('priceRate')->findOrFail($id);
        return view('category.edit', compact('category'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name' => $request->name
        ]);

        // Return JSON for AJAX handling
        return response()->json($category);
    }
    
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        
        // Return JSON for AJAX handling
        return response()->json(['success' => true, 'message' => 'تم حذف الفئة بنجاح']);
    }
}
