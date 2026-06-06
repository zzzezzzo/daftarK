<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? 'طباعة الباركود' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=amiri:400,700|figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 12px;
            font-family: 'Amiri', 'Figtree', serif;
            background: #e5e7eb;
            color: #111;
        }
        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            max-width: 210mm;
            margin: 0 auto 16px;
            padding: 12px 16px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }
        .toolbar a {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
        }
        .toolbar a:hover { text-decoration: underline; }
        .toolbar button {
            cursor: pointer;
            border: none;
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 600;
            font-size: 15px;
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            color: #fff;
        }
        .toolbar button:hover { opacity: .95; }
        .sheet {
            width: 210mm;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, 40mm);
            gap: 5mm; /* إضافة مسافة بسيطة بين الاستيكرات في العرض العادي */
            justify-content: start;
        }
        @media (max-width: 640px) {
            .sheet { grid-template-columns: 1fr; }
        }
        .label {
            width: 40mm;
            height: 25mm;
            background: #fff;
            border-radius: 2mm;
            padding: 2mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            overflow: hidden;
            page-break-inside: avoid;
            break-inside: avoid;
            border: 1px dashed #ccc; /* إطار خفيف للمعاينة فقط */
        }
        .label-name {
            font-size: 8pt;
            line-height: 1.1;
            margin-bottom: 1mm;
            max-height: 2.2em;
            overflow: hidden;
            text-align: center;
            font-weight: bold;
        }
        .barcode-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            padding: 0 4mm;
        }
        
        /* --- تحسينات الـ SVG للأكواد الطويلة --- */
        .barcode-wrap svg {
            width: 85% !important; /* صغرناه سنة عشان الخطوط متلزقش في حافة الاستيكر */
            height: 12mm !important; 
            shape-rendering: crispEdges; /* منع حواف الخطوط من التسييل أو الغبش */
        }

        .label-code {
            padding: 0 2mm;
            margin-top: 1mm;
            font-size: 7pt;
            word-break: break-all; /* لو الكود طويل جداً كـ نص ينزل سطر جديد */
        }
        .empty {
            max-width: 210mm;
            margin: 40px auto;
            text-align: center;
            padding: 24px;
            background: #fff;
            border-radius: 12px;
        }
        
        /* --- إعدادات الطباعة المدققة --- */
        @media print {
            @page {
                size: 40mm 30mm portrait; 
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
                background: #fff;
            }
            .toolbar { display: none !important; }
            .sheet {
                display: block; 
                width: 40mm;
                margin: 0;
            }
            .label {
                width: 40mm;
                height: 30mm; /* مطابقة تامة لمقاس رول الطابعة */
                border: none; 
                margin: 0;
                padding: 2mm 3mm; /* مساحة أمان داخلية للطباعة */
                page-break-after: always; 
                break-after: page;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
                box-sizing: border-box;
            }
            .barcode-wrap svg {
                width: 100% !important; /* ضمان استغلال كامل العرض في الاستيكر */
                height: 12mm !important;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <div>
            <strong>{{ $pageTitle ?? 'طباعة الباركود' }}</strong>
            @if(isset($products) && $products->count())
                <span style="color:#6b7280;font-size:13px;"> — {{ $products->count() }} ملصق</span>
            @endif
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <a href="{{ route('products.index') }}">← العودة للمنتجات</a>
            <button type="button" onclick="window.print()">طباعة</button>
        </div>
    </div>

    @if(!isset($products) || $products->isEmpty())
        <div class="empty">
            <p>لا توجد منتجات لعرض الباركود.</p>
            <a href="{{ route('products.index') }}">المنتجات</a>
        </div>
    @else
        <div class="sheet">
            @foreach($products as $product)
                <article class="label">
                    <div class="label-name">{{ $product->name }}</div>
                    <div class="barcode-wrap">
                        {{-- تم تغيير الـ factor من 2 إلى 1.2 عشان يناسب الأكواد الطويلة بدون ما تخرج بره الاستيكر --}}
                        {!! DNS1D::getBarcodeSVG($product->code, 'C128', 1, 50, '#000000', false, false) !!}
                    </div>
                    <div class="label-code">{{ $product->code }}</div>
                </article>
            @endforeach
        </div>
    @endif
</body>
</html>
