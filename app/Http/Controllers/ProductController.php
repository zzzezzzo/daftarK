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
        $products =$products->latest()->paginate(10)->withQueryString();
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
        $basePrice = $request->input('price_base', $product->price_base);
        $categoryId = $request->input('category_id', $product->category_id);
        
        $category = Category::with('priceRate')->find($categoryId);
        if (
            $validatedData['price_trade'] <= $basePrice || 
            $validatedData['price_technician'] <= $basePrice || 
            $validatedData['price_customer'] <= $basePrice
        ) {
            Alert::error('فشل', 'لا يمكن أن يكون سعر البيع أقل من أو يساوي سعر الشراء');
            return redirect()->back()->withInput(); 
        }
        if ($category && $category->priceRate) {
            $rate = $category->priceRate;
            $validatedData['price_trade'] =$request->price_trade ?? $basePrice + ($basePrice * ($rate->rate_trade / 100)) ;
            $validatedData['price_technician'] = $request->price_technician ?? $basePrice + ($basePrice * ($rate->rate_technician / 100));
            $validatedData['price_customer'] = $basePrice + ($basePrice * ($rate->rate_client / 100));
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

    /**
     * Printable sheet: barcode labels for every product (batch print).
     */
    public function printAllLabels()
    {
        $products = Product::query()->orderBy('name')->get();

        return view('product.labels-print', [
            'products' => $products,
            'pageTitle' => 'باركود جميع المنتجات',
        ]);
    }

    /**
     * Printable sheet: a single product label.
     */
    public function printOneLabel(Product $product)
    {
        return view('product.labels-print', [
            'products' => collect([$product]),
            'pageTitle' => 'باركود — ' . $product->name,
        ]);
    }
    public function printAllInStockLables(){
        $products = Product::where('stock','>', 0)->orderBy('name')->get();
        return view('product.labels-print', [
            'products' => $products,
            'pageTitle' => 'باركود المنتجات المتوفرة',
        ]);
    }

    /**
     * Excel-compatible export (SpreadsheetML .xls) of all products — opens in Microsoft Excel.
     */
    public function exportExcel()
    {
        $products = Product::with('category')->orderBy('name')->get();
        $xml = $this->buildProductsSpreadsheetXml($products);
        $filename = 'products-all-'.now()->format('Y-m-d-His').'.xls';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Styled SpreadsheetML (Excel 2003 XML): merged title, header band, zebra rows, column widths.
     *
     * @param  \Illuminate\Support\Collection<int, \App\Models\Product>  $products
     */
    private function buildProductsSpreadsheetXml($products): string
    {
        $colCount = 8;
        $mergeLast = $colCount - 1;
        $dataRows = $products->count();
        $expandedRows = 4 + $dataRows;

        $styles = <<<'XML'
<Styles>
  <Style ss:ID="Default" ss:Name="Normal">
    <Alignment ss:Vertical="Center"/>
    <Font ss:FontName="Segoe UI" ss:Size="11"/>
  </Style>
  <Style ss:ID="DocTitle">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>
    <Font ss:Bold="1" ss:Size="18" ss:Color="#FFFFFF"/>
    <Interior ss:Color="#1F4E79" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0D304E"/>
    </Borders>
  </Style>
  <Style ss:ID="DocMeta">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:Size="11" ss:Color="#2E4057"/>
    <Interior ss:Color="#D6EAF8" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#A9CCE3"/>
    </Borders>
  </Style>
  <Style ss:ID="Gap">
    <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="HeadCol">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>
    <Font ss:Bold="1" ss:Size="11" ss:Color="#FFFFFF"/>
    <Interior ss:Color="#2E75B6" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1F4E79"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1F4E79"/>
      <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1F4E79"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#0D304E"/>
    </Borders>
  </Style>
  <Style ss:ID="EvenTxt">
    <Alignment ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>
    <Font ss:Size="11"/>
    <Interior ss:Color="#F5FAFD" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D5E8F7"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D5E8F7"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
    </Borders>
  </Style>
  <Style ss:ID="OddTxt">
    <Alignment ss:Vertical="Center" ss:ReadingOrder="RightToLeft"/>
    <Font ss:Size="11"/>
    <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
    </Borders>
  </Style>
  <Style ss:ID="EvenNum">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:Size="11"/>
    <Interior ss:Color="#F5FAFD" ss:Pattern="Solid"/>
    <NumberFormat ss:Format="#,##0.00"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D5E8F7"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D5E8F7"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
    </Borders>
  </Style>
  <Style ss:ID="OddNum">
    <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
    <Font ss:Size="11"/>
    <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
    <NumberFormat ss:Format="#,##0.00"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
    </Borders>
  </Style>
  <Style ss:ID="EvenStock">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:Bold="1" ss:Size="11"/>
    <Interior ss:Color="#E8F6EF" ss:Pattern="Solid"/>
    <NumberFormat ss:Format="#,##0"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D5E8F7"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D5E8F7"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
    </Borders>
  </Style>
  <Style ss:ID="OddStock">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:Bold="1" ss:Size="11"/>
    <Interior ss:Color="#F4FFF9" ss:Pattern="Solid"/>
    <NumberFormat ss:Format="#,##0"/>
    <Borders>
      <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
      <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E4E4E4"/>
    </Borders>
  </Style>
</Styles>
XML;

        $lines = [];
        $lines[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $lines[] = '<?mso-application progid="Excel.Sheet"?>';
        $lines[] = '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">';
        $lines[] = $styles;
        $lines[] = '<Worksheet ss:Name="المنتجات">';
        $lines[] = '<Table ss:ExpandedColumnCount="'.$colCount.'" ss:ExpandedRowCount="'.$expandedRows.'" ss:FullColumns="1" ss:FullRows="1" ss:DefaultRowHeight="18">';
        $lines[] = '<Column ss:Width="96"/>';
        $lines[] = '<Column ss:Width="240"/>';
        $lines[] = '<Column ss:Width="130"/>';
        $lines[] = '<Column ss:Width="96"/>';
        $lines[] = '<Column ss:Width="96"/>';
        $lines[] = '<Column ss:Width="96"/>';
        $lines[] = '<Column ss:Width="96"/>';
        $lines[] = '<Column ss:Width="72"/>';

        $cellOut = static function (string $type, $value): string {
            if ($type === 'Number') {
                $out = is_numeric($value) ? (string) $value : htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
            } else {
                $out = htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
            }

            return $out;
        };

        $emitCell = static function (array $cell, callable $cellOut): string {
            $type = $cell['type'] ?? 'String';
            $value = $cell['value'] ?? '';
            $styleId = $cell['style'] ?? '';
            $mergeAcross = $cell['mergeAcross'] ?? null;
            $attrs = '';
            if ($styleId !== '') {
                $attrs .= ' ss:StyleID="'.htmlspecialchars($styleId, ENT_XML1 | ENT_QUOTES, 'UTF-8').'"';
            }
            if ($mergeAcross !== null) {
                $attrs .= ' ss:MergeAcross="'.(int) $mergeAcross.'"';
            }

            return '<Cell'.$attrs.'><Data ss:Type="'.$type.'">'.$cellOut($type, $value).'</Data></Cell>';
        };

        $row = static function (array $cells) use (&$lines, $cellOut, $emitCell): void {
            $lines[] = '<Row>';
            foreach ($cells as $cell) {
                $lines[] = $emitCell($cell, $cellOut);
            }
            $lines[] = '</Row>';
        };

        $row([
            [
                'type' => 'String',
                'value' => 'قائمة المنتجات — تصدير كامل',
                'style' => 'DocTitle',
                'mergeAcross' => $mergeLast,
            ],
        ]);
        $row([
            [
                'type' => 'String',
                'value' => 'تاريخ التصدير: '.now()->format('Y-m-d H:i').' — عدد السجلات: '.$dataRows,
                'style' => 'DocMeta',
                'mergeAcross' => $mergeLast,
            ],
        ]);
        $row([
            [
                'type' => 'String',
                'value' => '',
                'style' => 'Gap',
                'mergeAcross' => $mergeLast,
            ],
        ]);

        $row([
            ['type' => 'String', 'value' => 'الكود', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'اسم المنتج', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'الفئة', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'السعر الأساسي', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'سعر التجار', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'سعر الفنيين', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'سعر العملاء', 'style' => 'HeadCol'],
            ['type' => 'String', 'value' => 'المخزون', 'style' => 'HeadCol'],
        ]);

        $i = 0;
        foreach ($products as $product) {
            $even = $i % 2 === 0;
            $stTxt = $even ? 'EvenTxt' : 'OddTxt';
            $stNum = $even ? 'EvenNum' : 'OddNum';
            $stStk = $even ? 'EvenStock' : 'OddStock';
            $row([
                ['type' => 'String', 'value' => $product->code, 'style' => $stTxt],
                ['type' => 'String', 'value' => $product->name, 'style' => $stTxt],
                ['type' => 'String', 'value' => $product->category?->name ?? '', 'style' => $stTxt],
                ['type' => 'Number', 'value' => $product->price_base, 'style' => $stNum],
                ['type' => 'Number', 'value' => $product->price_trade, 'style' => $stNum],
                ['type' => 'Number', 'value' => $product->price_technician, 'style' => $stNum],
                ['type' => 'Number', 'value' => $product->price_customer, 'style' => $stNum],
                ['type' => 'Number', 'value' => $product->stock, 'style' => $stStk],
            ]);
            $i++;
        }

        $lines[] = '</Table>';
        $lines[] = '</Worksheet>';
        $lines[] = '</Workbook>';

        return implode("\n", $lines);
    }
}
