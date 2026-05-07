<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryPriceRateController;
use App\Http\Controllers\CashBoxController;
use App\Http\Controllers\customer\accountStatementController;
use App\Http\Controllers\customer\CustomerInvoiceController;
use App\Http\Controllers\customer\CustomerTransactionController;
use App\Http\Controllers\customer\customerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\supplier\accountStatement;
use App\Http\Controllers\supplier\SupplierInvoiceController;
use App\Http\Controllers\supplier\SupplierTransactionController;
use App\Http\Controllers\supplier\supplierContoller;
use App\Http\Controllers\wallet\walletController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth', 'is_admin:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
    // product routes
    Route::get('/products',[ProductController::class, 'index'])->name('products.index');
    Route::get('/products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
    Route::get('/products/labels/print', [ProductController::class, 'printAllLabels'])->name('products.labels.print');
    Route::get('/products/{product}/label/print', [ProductController::class, 'printOneLabel'])->name('products.label.print');
    Route::get('/products/in-stock/labels/print', [ProductController::class, 'printAllInStockLables'])->name('products.inStock.labels.print');
    Route::get('/products/create',[ProductController::class, 'create'])->name('products.create');
    Route::post('/products/create',[ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit',[ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}',[ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}',[ProductController::class, 'destroy'])->name('products.destroy');
    // create category
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    // categories index
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    // categories edit
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    // categories update
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    // categories destroy
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    // suppliers route
    Route::get('/suppliers', [supplierContoller::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [supplierContoller::class, 'create'])->name('suppliers.create');
    Route::post('/supplier/create', [supplierContoller::class,'store'])->name('suppliers.store');
    Route::get('/supplier/{id}/edit', [supplierContoller::class , 'edit'])->name('suppliers.edit');
    Route::put('supplier/{id}', [supplierContoller::class, 'update'])->name('suppliers.update');
    Route::delete('/supplier/{id}', [supplierContoller::class, 'destroy'])->name('suppliers.destroy');
    // supplier account management
    Route::get('/supplier/{id}/accountStatement',[accountStatement::class, 'index'])->name('accountStatement.index');
    Route::get('/supplier/{id}/accountStatement/transactions',[accountStatement::class, 'transactionIndex'])->name('supplierAccountStatement.transactionIndex');
    Route::get('/supplier/{id}/accountStatement/create',[accountStatement::class, 'create'])->name('accountStatement.create');
    Route::post('/supplier/{id}/accountStatement/create',[accountStatement::class, 'store'])->name('accountStatement.store');
    Route::get('/supplier/{id}/accountStatement/transaction/create',[accountStatement::class, 'createTransaction'])->name('supplierAccountStatement.createTransaction');
    Route::post('/supplier/{id}/accountStatement/transaction',[accountStatement::class, 'storeTransaction'])->name('supplierAccountStatement.storeTransaction');
    Route::get('/supplier/{id}/accountStatement/{invoiceId}/edit', [accountStatement::class, 'edit'])->name('accountStatement.edit');
    Route::get('/supplier/{id}/accountStatement/{invoiceId}/show', [accountStatement::class, 'show'])->name('accountStatement.show');
    Route::put('/supplier/{id}/accountStatement/{invoiceId}', [accountStatement::class, 'update'])->name('accountStatement.update');
    Route::delete('/supplier/{id}/accountStatement/{invoiceId}', [accountStatement::class, 'destroy'])->name('accountStatement.destroy');
    Route::post('/supplier/{id}/treasury/payment', [accountStatement::class, 'treasuryPayment'])->name('supplier.treasury.payment');    
    // supplier invoices and transactions
    Route::get('/supplier/invoices', [SupplierInvoiceController::class, 'index'])->name('supplier.invoices.index');
    Route::get('/supplier/transactions', [SupplierTransactionController::class, 'index'])->name('supplier.transactions.index');
    Route::get('/supplier/transactions/create', [SupplierTransactionController::class, 'create'])->name('supplier.transactions.create');
    Route::post('/supplier/transactions', [SupplierTransactionController::class, 'store'])->name('supplier.transactions.store');
    // customer Route
    Route::get('/customer', [customerController::class, 'index'])->name('customer.index');
    Route::get('/customer/create', [customerController::class, 'create'])->name('customer.create');
    Route::post('/customer/create', [customerController::class, 'store'])->name('customer.store');
    Route::get('/customer/{id}/edit', [customerController::class, 'edit'])->name('customer.edit');
    Route::put('/customer/{id}', [customerController::class, 'update'])->name('customer.update');
    Route::delete('/customer/{id}', [customerController::class, 'destroy'])->name('customer.destroy');
    // customer account mangement
    Route::get('/customer/{id}/accountStatement',[accountStatementController::class, 'index'])->name('customerAccountStatement.index');
    Route::get('/customer/{id}/accountStatement/export/excel', [accountStatementController::class, 'exportInvoicesExcel'])->name('customerAccountStatement.export.excel');
    Route::get('/customer/{id}/accountStatement/transactions',[accountStatementController::class, 'transactionIndex'])->name('customerAccountStatement.transactionIndex');
    Route::get('/customer/{id}/accountStatement/create',[accountStatementController::class, 'create'])->name('customerAccountStatement.create');
    Route::post('/customer/{id}/accountStatement/create',[accountStatementController::class, 'store'])->name('customerAccountStatement.store');
    Route::get('/customer/{id}/accountStatement/transaction/create',[accountStatementController::class,'createTransaction'])->name('customerAccountStatement.createTransaction');
    Route::post('/customer/{id}/accountStatement/transaction',[accountStatementController::class, 'storeTransaction'])->name('customerAccountStatement.storeTransaction');
    Route::get('/customer/{id}/accountStatement/{invoiceId}/edit', [accountStatementController::class, 'edit'])->name('customerAccountStatement.edit');
    Route::get('/customer/{id}/accountStatement/{invoiceId}/show', [accountStatementController::class, 'show'])->name('customerAccountStatement.show');
    Route::put('/customer/{id}/accountStatement/{invoiceId}', [accountStatementController::class, 'update'])->name('customerAccountStatement.update');
    Route::delete('/customer/{id}/accountStatement/{invoiceId}', [accountStatementController::class, 'destroy'])->name('customerAccountStatement.destroy');
    Route::post('/customer/{id}/treasury/payment', [accountStatementController::class, 'treasuryPayment'])->name('customer.treasury.payment');
    
    // customer invoices and transactions
    Route::get('/customer/invoices', [CustomerInvoiceController::class, 'index'])->name('customer.invoices.index');
    Route::get('/customer/transactions', [CustomerTransactionController::class, 'index'])->name('customer.transactions.index');
    Route::get('/customer/transactions/create', [CustomerTransactionController::class, 'create'])->name('customer.transactions.create');
    Route::post('/customer/transactions', [CustomerTransactionController::class, 'store'])->name('customer.transactions.store');
    // customer wallet
    Route::post('/wallet/{id}', [walletController::class, 'store'])->name('customerWallet.store');
    // cash box management
    Route::get('/cashBoxes', [CashBoxController::class, 'index'])->name('cashBoxes.index');
    Route::get('/cashBoxes/create', [CashBoxController::class, 'create'])->name('cashBoxes.create');
    Route::post('/cashBoxes', [CashBoxController::class, 'store'])->name('cashBoxes.store');
    Route::get('/cashBoxes/{cashBox}', [CashBoxController::class, 'show'])->name('cashBoxes.show');
    Route::post('/cashBoxes/{cashBox}/transaction', [CashBoxController::class, 'addTransaction'])->name('cashBoxes.addTransaction');
    Route::put('/cashBoxes/{cashBox}/close', [CashBoxController::class, 'close'])->name('cashBoxes.close');
    Route::put('/cashBoxes/{cashBox}/reopen', [CashBoxController::class, 'reopen'])->name('cashBoxes.reopen');
    Route::get('/cashBoxes/{cashBox}/report', [CashBoxController::class, 'report'])->name('cashBoxes.report');
    // category price rates
    Route::get('/categoryPriceRates', [CategoryPriceRateController::class, 'index'])->name('categoryPriceRates.index');
    Route::get('/categoryPriceRates/{categoryId}/create', [CategoryPriceRateController::class, 'create'])->name('categoryPriceRates.create');
    Route::post('/categoryPriceRates/{categoryId}', [CategoryPriceRateController::class, 'store'])->name('categoryPriceRates.store');
    Route::delete('/categoryPriceRates/{categoryId}', [CategoryPriceRateController::class, 'destroy'])->name('categoryPriceRates.destroy');














    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
