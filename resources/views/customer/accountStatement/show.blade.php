@php
    $totalQuantity = $invoice->items->sum('quantity');
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<x-app-layout>
    <div class="min-h-screen bg-zinc-200 py-6 px-3 dark:bg-zinc-950">
        <div class="max-w-[76mm] mx-auto w-full">

            <div id="invoice-print-section">
                <article class="bg-white text-black shadow-xl rounded-sm px-4 py-3 border border-zinc-200/80 receipt-font text-right" dir="rtl">

                    <header class="text-center border-b border-dashed border-black pb-2 mb-2">
                        <h1 class="text-xl font-extrabold tracking-tight text-black m-0">محل ابو يزيد</h1>
                        <p class="text-xs text-zinc-700 font-semibold mt-0.5 mb-0">مشتول السوق</p>
                        <p class="text-xs text-zinc-700 font-bold tabular-nums m-0">01270042606</p>
                    </header>

                    <section class="text-xs text-black mb-2 border-b border-dashed border-black pb-2 space-y-1">
                        <div class="flex justify-between gap-2">
                            <span class="text-zinc-600 font-medium">فاتورة</span>
                            <span class="font-bold tabular-nums">#{{ $invoice->invoice_number }}</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="text-zinc-600 font-medium">التاريخ والوقت</span>
                            <span class="font-bold tabular-nums">
                                {{ $invoice->date }} 
                                <span class="text-zinc-700 font-normal mr-1">
                                    {{ $invoice->created_at ? $invoice->created_at->format('g:i A') : '' }}
                                </span>
                            </span>
                        </div>
                        <div class="flex justify-between gap-2 items-start">
                            <span class="text-zinc-600 font-medium shrink-0">العميل</span>
                            <span class="font-extrabold text-right leading-none">{{ $customer->name }}</span>
                        </div>
                        @if($customer->phone)
                            <div class="flex justify-between gap-2">
                                <span class="text-zinc-600 font-medium">رقم الهاتف</span>
                                <span class="tabular-nums font-bold">{{ $customer->phone }}</span>
                            </div>
                        @endif
                    </section>

                    <section class="mb-2">
                        <table class="w-full text-right border-collapse border border-black receipt-table text-xs">
                            <thead>
                                <tr class="bg-zinc-100 text-[11px] font-bold border-b border-black">
                                    <th class="border-l border-black text-gray-900 p-1 text-right w-[42%]">المنتج</th>
                                    <th class="border-l border-black text-gray-900 p-1 text-center w-[18%]">السعر</th>
                                    <th class="border-l border-black text-gray-900 p-1 text-center w-[15%]">الكمية</th>
                                    <th class="text-gray-900 p-1 text-left w-[25%]">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                    @php
                                        $lineTotal = $item->quantity * $item->unit_price;
                                    @endphp
                                    <tr class="border-b border-black last:border-b-0">
                                        <td class="border-l border-black text-gray-900 p-1 font-bold leading-tight text-right text-[12px]">
                                            {{ $item->product->name }}
                                        </td>
                                        <td class="border-l border-black p-1 text-center text-black tabular-nums font-bold whitespace-nowrap">
                                            {{ number_format($item->unit_price, 2, '.', '') }}
                                        </td>
                                        <td class="border-l text-gray-900 border-black p-1 text-center tabular-nums font-extrabold">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="p-1 text-gray-900 text-left tabular-nums font-extrabold">
                                            {{ number_format($lineTotal, 2, '.', '') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </section>

                    <section class="space-y-1 text-xs mb-2 pt-1">
                        <div class="flex justify-between gap-2">
                            <span class="text-zinc-700 font-medium">إجمالي الكمية</span>
                            <span class="font-extrabold tabular-nums text-[13px]">{{ $totalQuantity }}</span>
                        </div>
                        <div class="flex justify-between gap-2 items-baseline border-t border-dashed border-black mt-1 pt-2">
                            <span class="text-sm font-extrabold">الإجمالي</span>
                            <span class="text-base font-black tabular-nums">{{ number_format($invoice->total_amount, 2) }} ج.م</span>
                        </div>
                        <div class="flex justify-between gap-2 pt-0.5">
                            <span class="text-zinc-700 font-medium">المدفوع</span>
                            <span class="tabular-nums font-extrabold">{{ number_format($invoice->paid_amount, 2) }} ج.م</span>
                        </div>
                        <div class="flex justify-between gap-2">
                            <span class="text-zinc-700 font-medium">المتبقي</span>
                            <span class="tabular-nums font-extrabold">{{ number_format($invoice->remining_amount, 2) }} ج.م</span>
                        </div>
                    </section>

                    <footer class="text-center border-t border-dashed border-black pt-2 mt-2">
                        <p class="text-sm font-extrabold tracking-wide">شكراً لكم</p>
                        <p class="text-[9px] text-zinc-600 uppercase tracking-wider font-sans font-semibold m-0">Thank you</p>
                    </footer>

                </article>
            </div>

            <div class="flex flex-col gap-4 mt-5">
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('customerAccountStatement.index', $customer->id) }}"
                       class="flex items-center justify-center gap-2 bg-zinc-700 text-white px-6 py-3 rounded-xl hover:bg-zinc-800 transition-colors shadow-md text-sm font-semibold">
                        <i class="bi bi-arrow-right"></i>
                        رجوع للقائمة
                    </a>
                    <button type="button" onclick="printThermalInvoice()"
                            class="flex items-center justify-center gap-2 bg-gray-900 text-white px-6 py-3 rounded-xl hover:bg-black transition-colors shadow-md text-sm font-semibold">
                        <i class="bi bi-printer-fill"></i>
                        طباعة الفاتورة
                    </button>
                    <button type="button"
                            onclick="window.open('{{ route('customerAccountStatement.edit', [$customer->id, $invoice->id]) }}', '_blank')"
                            class="flex items-center justify-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-xl hover:bg-indigo-700 transition-colors shadow-md text-sm font-semibold">
                        <i class="bi bi-pencil-square"></i>
                        تعديل الفاتورة
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
        function printThermalInvoice() {
            var printContents = document.getElementById('invoice-print-section').innerHTML;
            var printWindow = window.open('', '_blank', 'height=600,width=400');
            
            printWindow.document.write('<html><head><title>طباعة الفاتورة</title>');
            printWindow.document.write('<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"><\/script>');
            printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">');
            
            printWindow.document.write('<style>');
            printWindow.document.write('html, body { font-family: "Cairo", sans-serif !important; margin: 0; padding: 0; background: #fff; width: 76mm; height: auto; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }');
            printWindow.document.write('@page { size: 76mm auto; margin: 0mm; }');
            
            /* الحيلة السحرية لمنع Firefox من تكرار رأس الجدول المزعج */
            printWindow.document.write('thead { display: table-row-group !important; }'); /* تحويل الرأس لمنع التكرار التلقائي */
            printWindow.document.write('thead tr { page-break-inside: avoid !important; break-inside: avoid !important; }');
            printWindow.document.write('tr, table, section { page-break-inside: avoid !important; break-inside: avoid !important; }');
            printWindow.document.write('<\/style>');
            
            printWindow.document.write('</head><body dir="rtl">');
            printWindow.document.write('<div style="width: 76mm; max-width: 76mm; padding: 4px; box-sizing: border-box;">' + printContents + '</div>');
            printWindow.document.write('</body></html>');
            
            printWindow.document.close();
            
            setTimeout(function () {
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }, 600);
        }
    </script>

    <style>
        .receipt-font {
            font-family: 'Cairo', ui-sans-serif, system-ui, -apple-system, sans-serif !important;
        }
        .receipt-table th, .receipt-table td {
            vertical-align: middle;
        }
    </style>
</x-app-layout>