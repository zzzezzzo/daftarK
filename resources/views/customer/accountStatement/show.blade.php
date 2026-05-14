@php
    $totalQuantity = $invoice->items->sum('quantity');
@endphp

<x-app-layout>
    <div id="print-area"
         class="thermal-receipt-root min-h-screen bg-zinc-200 py-6 px-3 print:bg-white print:py-0 print:min-h-0 dark:bg-zinc-950">
        <div class="max-w-[22rem] mx-auto w-full print:w-full print:max-w-none print:mx-0">

            <article
                class="thermal-receipt-paper bg-white text-gray-900 shadow-xl rounded-sm px-5 py-1 border border-zinc-200/80 print:shadow-none print:border-0 print:rounded-none receipt-font">

                <!-- Store header -->
                <header class="text-center border-b border-dotted border-zinc-400 pb-4 mb-1 print:pb-3 print:mb-3">
                    <h1 class="text-xl font-bold tracking-tight text-gray-900 print:text-[15pt] print:leading-tight">محل ابو يزيد</h1>
                    <p class="text-xs text-gray-600 leading-relaxed print:text-[10pt]">مشتول السوق</p>
                    <p class="text-xs text-gray-600 print:text-[10pt]">01270042606</p>
                </header>

                <!-- Transaction meta -->
                <section class="text-xs text-gray-700 mb-1 border-b border-dotted border-zinc-400 pb-4 print:text-[10pt] print:space-y-1.5 print:pb-3">
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-500">فاتورة</span>
                        <span class="font-semibold tabular-nums">#{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-500">التاريخ</span>
                        <span class="font-medium tabular-nums">{{ $invoice->date }}</span>
                    </div>
                    <div class="flex justify-between gap-3 items-start">
                        <span class="text-gray-500 shrink-0">العميل</span>
                        <span class="font-medium text-right leading-snug">{{ $customer->name }}</span>
                    </div>
                    @if($customer->phone)
                        <div class="flex justify-between gap-3">
                            <span class="text-gray-500">رقم الهاتف</span>
                            <span class="tabular-nums">{{ $customer->phone }}</span>
                        </div>
                    @endif
                </section>

                <!-- Line items -->
                <section class="mb-4 border-b border-dotted border-zinc-400 pb-4 print:mb-3 print:pb-3">
                    <!-- Table Header -->
                    <div class="grid grid-cols-4 border border-black text-[11px] font-bold text-gray-700 print:text-[9pt]">
                        
                        <div class="border-l border-black p-1 text-right">
                            المنتج
                        </div>
                        <div class="border-l border-black p-1 text-center">
                            السعر
                        </div>
                        <div class="border-l border-black p-1 text-center">
                            الكمية
                        </div>

                        <div class="p-1 text-left">
                            الإجمالي
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="border-r border-l border-b border-black">

                        @foreach($invoice->items as $item)

                            @php
                                $lineTotal = $item->quantity * $item->unit_price;
                            @endphp

                            <div class="grid grid-cols-4 text-sm print:text-[11pt] border-b border-black last:border-b-0">

                                <!-- Product -->
                                <div class="border-l border-black p-1 text-[15px] font-bold break-words">
                                    {{ $item->product->name }}
                                </div>

                                <!-- Price -->
                                <div class="border-l border-black p-1 text-center tabular-nums font-semibold">
                                    {{ $item->unit_price }}
                                </div>

                                <!-- Quantity -->
                                <div class="border-l border-black p-1 text-center tabular-nums font-semibold">
                                    {{ $item->quantity }}
                                </div>

                                <!-- Total -->
                                <div class="p-1 text-left tabular-nums font-semibold">
                                    {{ number_format($lineTotal, 2) }}
                                </div>

                            </div>

                        @endforeach

                    </div>

                </section>

                <!-- Totals -->
                <section class="space-y-2 text-sm mb-1 print:text-[10pt] print:space-y-1 print:mb-1">
                    <div class="flex justify-between gap-3">
                        <span class="text-gray-600">إجمالي الكمية</span>
                        <span class="font-semibold tabular-nums">{{ $totalQuantity }}</span>
                    </div>
                    <div class="flex justify-between gap-3 items-baseline border-t border-dotted border-zinc-400 mt-2 pt-3 print:mt-1.5 print:pt-2">
                        <span class="text-base font-bold text-gray-900 print:text-[12pt]">الإجمالي</span>
                        <span class="text-lg font-bold tabular-nums print:text-[13pt]">{{ number_format($invoice->total_amount, 2) }} ج.م</span>
                    </div>
                    <div class="flex justify-between gap-3 text-xs pt-1 print:text-[10pt]">
                        <span class="text-gray-600">المدفوع</span>
                        <span class="tabular-nums font-medium">{{ number_format($invoice->paid_amount, 2) }} ج.م</span>
                    </div>
                    <div class="flex justify-between gap-3 text-xs print:text-[10pt]">
                        <span class="text-gray-600">المتبقي</span>
                        <span class="tabular-nums font-medium">{{ number_format($invoice->remining_amount, 2) }} ج.م</span>
                    </div>
                </section>

                <footer class="text-center border-t border-dotted border-zinc-400 pt-1 print:pt-1">
                    <p class="text-sm font-bold text-gray-900 tracking-wide print:text-[11pt]">شكراً لكم</p>
                    <p class="text-[10px] text-gray-500 mt-1 print:text-[8pt]">Thank you</p>
                </footer>

            </article>

            <!-- Actions (screen only) -->
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
        .receipt-font {
            font-family: ui-monospace, SFMono-Regular, "Cascadia Mono", "Segoe UI Mono", Menlo, Consolas, monospace;
        }

        @media print {
            @page {
                size: auto;
                margin: 2mm;
            }
            /* إخفاء الأزرار */
            .no-print  , nav{
                display: none !important;
            }

            #print-area.thermal-receipt-root {
                position: static !important;
                left: auto !important;
                top: auto !important;
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                min-height: 0 !important;
                height: auto !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            #print-area.thermal-receipt-root,
            #print-area.thermal-receipt-root * {
                max-height: none !important;
                overflow: visible !important;
            }

            #print-area.thermal-receipt-root > div {
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
                width: 100% !important;
            }

            #print-area.thermal-receipt-root .thermal-receipt-paper {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 3mm 4mm !important;
                box-sizing: border-box !important;
                height: auto !important;
                box-shadow: none !important;
                border: none !important;
            }

    </style>

</x-app-layout>