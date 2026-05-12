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

            /* عرض الاستيكر 40mm */
            grid-template-columns: repeat(auto-fill, 40mm);

            gap: 2mm;
            justify-content: start;
        }
        @media (max-width: 640px) {
            .sheet { grid-template-columns: 1fr; }
        }
        .label {
            width: 40mm;
            height: 30mm;

            background: #fff;
            border-radius: 2mm;

            padding: 2mm;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;

            border: 1px solid #d1d5db;

            overflow: hidden;

            page-break-inside: avoid;
            break-inside: avoid;
        }
        .label-name {
            font-size: 7pt;
            line-height: 1.1;
            margin-bottom: 1mm;

            max-height: 2.2em;
            overflow: hidden;
        }
        .barcode-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .barcode-wrap svg {
            width: 100%;
            height: 10mm !important;
        }

        .label-code {
            margin-top: 1mm;
            font-size: 7pt;
        }
        .empty {
            max-width: 210mm;
            margin: 40px auto;
            text-align: center;
            padding: 24px;
            background: #fff;
            border-radius: 12px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none !important; }
            .sheet {
                gap: 2mm;
                max-width: none;
            }
            .label {
                border: 1px solid #ccc;
                box-shadow: none;
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
                        {!! DNS1D::getBarcodeSVG($product->code, 'C128', 1.35, 46, '#000000', false, true) !!}
                    </div>
                    <div class="label-code">{{ $product->code }}</div>
                </article>
            @endforeach
        </div>
    @endif
</body>
</html>
