<?php
use App\Http\Controllers\Team\Invoice\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:invoice:show'])->group(function () {
    Route::resource('invoice', InvoiceController::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])
        ->names('invoice');


        Route::get('payment/{id}/edit', [InvoiceController::class, 'editPayment'])->name('payment.Edit');
        Route::put('payment/{id}', [InvoiceController::class, 'updatePayment'])->name('payment.Update');
        Route::delete('payment/{id}', [InvoiceController::class, 'destroyPayment'])->name('payment.destroy');
        Route::get('print-payment/{id}', [InvoiceController::class, 'printPayment'])->name('payment.print');

    Route::get('invoice/pending', [InvoiceController::class, 'InvoicePending'])
        ->name('invoice.pending');
    Route::get('invoice/complete', [InvoiceController::class, 'InvoiceComplete'])
        ->name('invoice.complete');
});

Route::middleware(['permission:invoice:export'])->group(function () {
    Route::post('invoice/export', [InvoiceController::class, 'exportInvoice'])->name('invoice.export');
});
