<?php

namespace App\Http\Controllers\wallet;
use App\Models\CashBox;
use App\Models\Customer;
use App\Models\CustomerInvoice;
use App\Http\Controllers\Controller;
use App\Models\CustomerTransaction;
use App\Models\CustomerWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class walletController extends Controller
{
    public function store(Request $request, $customerId)
    {
        $request->validate([
            'balance' => 'required|numeric|min:0'
        ]);

        $amount = $request->balance;
        $customer = Customer::findOrFail($customerId);
        DB::beginTransaction();
        try {
            // Wallet
            $wallet = CustomerWallet::where('customer_id', $customerId)
                ->lockForUpdate()
                ->first();

            if (!$wallet) {
                $wallet = CustomerWallet::create([
                    'customer_id' => $customerId,
                    'balance' => 0
                ]);
            }
            // تسجيل الإيداع
            $tx = new CustomerTransaction();
            $tx->customer_id = $customerId;
            $tx->amount = $amount;
            $tx->description = "إيداع رصيد للمحفظة";
            $tx->transaction_date = now();
            $tx->reference_type = 'wallet_deposit';
            $tx->reference_id = $wallet->id;
            $tx->method = 'cash';
            $tx->type = 'deposit';
            $tx->save();
            // إضافة الرصيد للمحفظة
            $wallet->balance += $amount;
            $wallet->save();
            // add the login to the cashbox and transaction cashbox
            $cashBox = CashBox::where('status', 'active')->first();
            if ($cashBox) {
                $cashBox->current_balance += $amount;
                $cashBox->total_in += $amount;
                $cashBox->save();
                $cashBox->transactions()->create([
                    'amount' => $amount,
                    'type' => 'in',
                    'description' => "إيداع من العميل  $customer->name",
                    'reference_id' => $tx->id,
                    'reference_type' => 'customer_transaction',
                    'user_id' => auth()->id()
                ]);
            }



            // جلب الفواتير غير المدفوعة
            $invoices = CustomerInvoice::where('customer_id', $customerId)
                ->whereIn('state', ['unpaid', 'partial'])
                ->where('remining_amount', '>', 0)
                ->orderBy('created_at')
                ->lockForUpdate()
                ->get();

            foreach ($invoices as $invoice) {
                if ($wallet->balance <= 0) {
                    break;
                }
                $reminingAmount = $invoice->remining_amount;
                if ($wallet->balance >= $reminingAmount) {
                    // دفع كامل
                    $paymentAmount = $reminingAmount;

                    $wallet->balance -= $paymentAmount;

                    $invoice->paid_amount += $paymentAmount;
                    $invoice->remining_amount = 0;
                    $invoice->state = 'paid';
                    $invoice->save();

                    CustomerTransaction::create([
                        'customer_id' => $customerId,
                        'amount' => $paymentAmount,
                        'type' => 'payment'
                    ]);

                } else {
                    // دفع جزئي
                    $paymentAmount = $wallet->balance;

                    $invoice->paid_amount += $paymentAmount;
                    $invoice->remining_amount = $reminingAmount - $paymentAmount;
                    $invoice->state = 'partial';
                    $invoice->save();

                    CustomerTransaction::create([
                        'customer_id' => $customerId,
                        'amount' => $paymentAmount,
                        'type' => 'payment'
                    ]);

                    $wallet->balance = 0;
                }
            }
            $wallet->save();
            // ✅ حفظ كل العمليات
            DB::commit();

            return redirect()->route('customerAccountStatement.index', $customerId);

        } catch (\Exception $e) {

            // ❌ في حالة أي خطأ
            DB::rollBack();

            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }   
    }
}
