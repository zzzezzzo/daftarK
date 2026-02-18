<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryPriceRate;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class CategoryPriceRateController extends Controller
{
    /**
     * Show form to create/edit price rates for a category
     */
    public function create($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        $priceRate = $category->priceRate;
        
        return view('categoryPriceRates.create', compact('category', 'priceRate'));
    }

    /**
     * Store or update price rates for a category
     */
    public function store(Request $request, $categoryId)
    {
        $request->validate([
            'rate_trade' => 'required|numeric|min:0|max:999.99',
            'rate_technician' => 'required|numeric|min:0|max:999.99',
            'rate_client' => 'required|numeric|min:0|max:999.99',
        ]);

        $category = Category::findOrFail($categoryId);
        
        // Update or create price rate
        $category->priceRate()->updateOrCreate(
            ['category_id' => $categoryId],
            [
                'rate_trade' => $request->rate_trade,
                'rate_technician' => $request->rate_technician,
                'rate_client' => $request->rate_client,
            ]
        );
        Alert::success('نجاح', 'تم اضافة النسب بنجاح');
        return redirect()
            ->route('categories.index', $categoryId)
            ->with('success', 'تم تحديث أسعار الفئة بنجاح');
    }

    /**
     * Show price rates for all categories
     */
    public function index()
    {
        $categories = Category::with('priceRate')->paginate(5);
        return view('categoryPriceRates.index', compact('categories'));
    }

    /**
     * Delete price rate for a category
     */
    public function destroy($categoryId)
    {
        $category = Category::findOrFail($categoryId);
        
        if ($category->priceRate) {
            $category->priceRate->delete();
        }

        return redirect()
            ->route('categoryPriceRates.index')
            ->with('success', 'تم حذف أسعار الفئة بنجاح');
    }
}
