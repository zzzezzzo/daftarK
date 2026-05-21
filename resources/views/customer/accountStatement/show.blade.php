@php
    $totalQuantity = $invoice->items->sum('quantity');
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<x-app-layout>
    <div id="print-area"
         class="thermal-receipt-root min-h-screen bg-zinc-200 py-6 px-3 print:bg-white print:py-0 print:min-h-0 dark:bg-zinc-950">
        <div class="max-w-[76mm] mx-auto w-full print:w-[76mm] print:max-w-none print:mx-0">

            <article
                class="thermal-receipt-paper bg-white text-black shadow-xl rounded-sm px-3 py-2 border border-zinc-200/80 print:shadow-none print:border-0 print:rounded-none receipt-font text-right" dir="rtl">

                <header class="text-center border-b border-dashed border-black pb-2 mb-2">
                    <h1 class="text-xl font-extrabold tracking-tight text-black print:text-[15pt] print:leading-tight">محل ابو يزيد</h1>
                    <p class="text-xs text-zinc-700 font-semibold print:text-[10pt] mt-0.5">مشتول السوق</p>
                    <p class="text-xs text-zinc-700 font-bold tabular-nums print:text-[10pt]">01270042606</p>
                </header>

                <section class="text-xs text-black mb-2 border-b border-dashed border-black pb-2 print:text-[9pt] space-y-1">
                    <div class="flex justify-between gap-2">
                        <span class="text-zinc-600 font-medium print:text-black">فاتورة</span>
                        <span class="font-bold tabular-nums">#{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-zinc-600 font-medium print:text-black">التاريخ</span>
                        <span class="font-bold tabular-nums">{{ $invoice->date }}</span>
                    </div>
                    <div class="flex justify-between gap-2 items-start">
                        <span class="text-zinc-600 font-medium print:text-black shrink-0">العميل</span>
                        <span class="font-extrabold text-right leading-none">{{ $customer->name }}</span>
                    </div>
                    @if($customer->phone)
                        <div class="flex justify-between gap-2">
                            <span class="text-zinc-600 font-medium print:text-black">رقم الهاتف</span>
                            <span class="tabular-nums font-bold">{{ $customer->phone }}</span>
                        </div>
                    @endif
                </section>

                <section class="mb-2">
                    <table class="w-full text-right border-collapse border border-black receipt-table">
                        <thead>
                            <tr class="bg-zinc-100 print:bg-transparent text-[11px] font-bold print:text-[9.5pt] border-b border-black">
                                <th class="border-l border-black p-1 text-right w-[42%]">المنتج</th>
                                <th class="border-l border-black p-1 text-center w-[18%]">السعر</th>
                                <th class="border-l border-black p-1 text-center w-[15%]">الكمية</th>
                                <th class="p-1 text-left w-[25%]">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                                @php
                                    $lineTotal = $item->quantity * $item->unit_price;
                                @endphp
                                <tr class="text-xs print:text-[10pt] border-b border-black last:border-b-0 break-inside-avoid">
                                    <td class="border-l border-black p-1 font-bold leading-tight text-right text-[12px] print:text-[10pt]">
                                        {{ $item->product->name }}
                                    </td>
                                    <td class="border-l border-black p-1 text-center tabular-nums font-bold whitespace-nowrap">
                                        {{ number_format($item->unit_price, 2, '.', '') }}
                                    </td>
                                    <td class="border-l border-black p-1 text-center tabular-nums font-extrabold">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="p-1 text-left tabular-nums font-extrabold">
                                        {{ number_format($lineTotal, 2, '.', '') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>

                <section class="space-y-1 text-xs mb-2 pt-1 print:text-[9.5pt]">
                    <div class="flex justify-between gap-2">
                        <span class="text-zinc-700 font-medium print:text-black">إجمالي الكمية</span>
                        <span class="font-extrabold tabular-nums text-[13px]">{{ $totalQuantity }}</span>
                    </div>
                    <div class="flex justify-between gap-2 items-baseline border-t border-dashed border-black mt-1 pt-2">
                        <span class="text-sm font-extrabold print:text-[11pt]">الإجمالي</span>
                        <span class="text-base font-black tabular-nums print:text-[13pt]">{{ number_format($invoice->total_amount, 2) }} ج.م</span>
                    </div>
                    <div class="flex justify-between gap-2 pt-0.5">
                        <span class="text-zinc-700 font-medium print:text-black">المدفوع</span>
                        <span class="tabular-nums font-extrabold">{{ number_format($invoice->paid_amount, 2) }} ج.م</span>
                    </div>
                    <div class="flex justify-between gap-2">
                        <span class="text-zinc-700 font-medium print:text-black">المتبقي</span>
                        <span class="tabular-nums font-extrabold">{{ number_format($invoice->remining_amount, 2) }} ج.م</span>
                    </div>
                </section>

                <footer class="text-center border-t border-dashed border-black pt-2 mt-2">
                    <p class="text-sm font-extrabold tracking-wide print:text-[11pt]">شكراً لكم</p>
                    <p class="text-[9px] text-zinc-600 uppercase tracking-wider print:text-[8pt] font-sans font-semibold">Thank you</p>
                </footer>

            </article>

            <div class="no-print flex flex-col gap-4 mt-5">
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('customerAccountStatement.index', $customer->id) }}"
                       class="flex items-center justify-center gap-2 bg-zinc-700 text-white px-6 py-3 rounded-xl hover:bg-zinc-800 transition-colors shadow-md text-sm font-semibold">
                        <i class="bi bi-arrow-right"></i>
                        رجوع للقائمة
                    </a>
                    <button type="button" onclick="window.print()"
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

    <style>
        /* تطبيق خط Cairo الاحترافي ليكون هو الأساس للفاتورة بأكملها */
        .receipt-font {
            font-family: 'Cairo', ui-sans-serif, system-ui, -apple-system, sans-serif !important;
        }
        
        .receipt-table th, .receipt-table td {
            vertical-align: middle;
        }

        @media print {
            /* إجبار المتصفح على تحميل الخطوط واستخدامها أثناء الطباعة */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            @page {
                size: 76mm auto;
                margin: 0mm 2mm 0mm 2mm;
            }
            
            html, body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                font-family: 'Cairo', sans-serif !important;
            }

            .no-print, nav, header:first-of-type {
                display: none !important;
            }

            #print-area.thermal-receipt-root {
                position: static !important;
                width: 76mm !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                min-height: 0 !important;
                height: auto !important;
                background: #fff !important;
            }

            #print-area.thermal-receipt-root .thermal-receipt-paper {
                width: 76mm !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 2mm 1mm !important;
                box-sizing: border-box !important;
                height: auto !important;
                border: none !important;
                box-shadow: none !important;
            }
            
            .break-inside-avoid {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</x-app-layout>