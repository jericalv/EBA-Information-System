<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\PendingRegistrationController;
use App\Http\Controllers\Cashier\CashierController;
use App\Http\Controllers\Concessionaire\ConcessionaireController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\Faculty\FacultyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PartnershipApplicationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::post('/register', [PendingRegistrationController::class, 'store'])->name('register.store');
    Route::post('/register/pending', [PendingRegistrationController::class, 'store'])->name('register.pending');
    Route::get('/register/confirm/{token}', [PendingRegistrationController::class, 'confirm'])->name('register.confirm');
});

Route::middleware(['restrict.admin.panel'])->group(function () {
    Route::view('/', 'welcome')->name('home');
});

Route::middleware(['auth', 'verified', 'restrict.admin.panel'])->group(function () {
    Route::redirect('dashboard', '/')->name('dashboard');
});

// Partnership Applications (authenticated users)
Route::middleware(['auth', 'restrict.admin.panel'])->group(function () {
    Route::get('/partnership/apply', [PartnershipApplicationController::class, 'create'])->name('partnership.apply');
    Route::post('/partnership/apply', [PartnershipApplicationController::class, 'store'])->name('partnership.store');
    Route::get('/application', [PartnershipApplicationController::class, 'show'])->name('application');
    Route::post('/application/loi', [PartnershipApplicationController::class, 'uploadLOI'])->name('application.loi');
    Route::post('/application/form', [PartnershipApplicationController::class, 'submitWizardForm'])->name('application.wizard-form');
    Route::post('/application/receipt', [PartnershipApplicationController::class, 'uploadReceipt'])->name('application.receipt');
    Route::patch('/application/info', [PartnershipApplicationController::class, 'updateApplicantInfo'])->name('application.info');
    Route::post('/application/resubmit', [PartnershipApplicationController::class, 'resubmitApplication'])->name('application.resubmit');

    Route::redirect('/settings/application', '/application')->name('settings.application');
    Route::redirect('/partnership/status', '/application');

    Route::post('/partnership/documents', [PartnershipApplicationController::class, 'uploadDocument'])->name('partnership.documents.upload');
});

// Invoice download from payment emails (signed URL, no login required)
Route::get('/invoices/{payment}', [InvoiceController::class, 'download'])
    ->name('invoice.download')
    ->middleware('signed');

// Admin Authentication (public)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Admin Panel (protected)
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/notifications/mark-read', [AdminController::class, 'markApplicationNotificationsRead'])->name('notifications.mark-read');
    Route::get('/transaction-logs', [AdminController::class, 'transactionLogs'])->name('transaction-logs');
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/concessionaires', [AdminController::class, 'paymentsIndex'])->name('concessionaires');
    Route::get('/payment-logs', [AdminController::class, 'paymentLogs'])->name('payment-logs');
    Route::get('/payments/history/view', [CashierController::class, 'viewHistoryPdf'])->name('payments.history.view');
    Route::get('/payments/history/pdf', [CashierController::class, 'downloadHistoryPdf'])->name('payments.history.pdf');
    Route::get('/payments/history/csv', [CashierController::class, 'downloadHistoryCsv'])->name('payments.history.csv');
    Route::get('/payments/{concessionaire}/history/view', [CashierController::class, 'viewConcessionaireHistoryPdf'])->name('payments.concessionaire.history.view');
    Route::get('/payments/{concessionaire}/history/pdf', [CashierController::class, 'downloadConcessionaireHistoryPdf'])->name('payments.concessionaire.history.pdf');
    Route::get('/payments/{concessionaire}/history/csv', [CashierController::class, 'downloadConcessionaireHistoryCsv'])->name('payments.concessionaire.history.csv');
    Route::get('/payments/{payment}/receipt', [CashierController::class, 'downloadReceipt'])->name('payments.receipt');
    Route::get('/record-payment', [AdminController::class, 'recordPayment'])->name('record-payment');
    Route::post('/record-payment', [AdminController::class, 'storeRecordedPayment'])->name('record-payment.store');
    Route::get('/uniform-checkout', [AdminController::class, 'uniformCheckout'])->name('uniform-checkout');
    Route::get('/reviews', [AdminController::class, 'reviewsIndex'])->name('reviews');
    Route::delete('/reviews/product/{id}', [AdminController::class, 'deleteProductReview'])->name('reviews.delete-product');
    Route::delete('/reviews/store/{id}', [AdminController::class, 'deleteStoreReview'])->name('reviews.delete-store');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/staff/create', [AdminController::class, 'createStaffAccount'])->name('staff.create');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('users.updateRole');
    Route::patch('/users/{user}/business-name', [AdminController::class, 'updateBusinessName'])->name('users.updateBusinessName');
    Route::patch('/users/{user}/monthly-fee', [AdminController::class, 'updateMonthlyFee'])->name('users.monthly-fee');
    Route::get('/users/{user}/details', [AdminController::class, 'userDetails'])->name('users.details');
    Route::post('/users/{user}/send-password-reset', [AdminController::class, 'sendPasswordReset'])->name('users.sendPasswordReset');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    
    // Uniform Stocks
    Route::get('/stocks', [AdminController::class, 'stocks'])->name('stocks');
    Route::get('/stocks/create', [AdminController::class, 'createStock'])->name('stocks.create');
    Route::get('/stocks/{stock}/edit', [AdminController::class, 'editStock'])->name('stocks.edit');
    Route::post('/stocks', [AdminController::class, 'storeStock'])->name('stocks.store');
    Route::patch('/stocks/{stock}/adjust', [AdminController::class, 'adjustStock'])->name('stocks.adjust');
    Route::patch('/stocks/{stock}', [AdminController::class, 'updateStock'])->name('stocks.update');
    Route::delete('/stocks/{id}', [AdminController::class, 'confirmDelete'])->name('stocks.delete');
    Route::delete('/stocks/{stock}/destroy', [AdminController::class, 'destroyStock'])->name('stocks.destroy');
    
    // System Logs
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
    Route::delete('/logs', [AdminController::class, 'clearLogs'])->name('logs.clear');
    
    // Site Settings CMS
    Route::get('/site-settings', [AdminController::class, 'siteSettings'])->name('site-settings');
    Route::post('/site-settings', [AdminController::class, 'updateSiteSettings'])->name('site-settings.update');

    // Activity Logs
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');
    
    // Partnership Applications
    Route::get('/partnerships', [AdminController::class, 'partnerships'])->name('partnerships');
    Route::post('/partnerships/{id}/reject', [AdminController::class, 'rejectPartnership'])->name('partnerships.reject');
    Route::patch('/partnerships/{application}/contract-period', [AdminController::class, 'saveContractPeriod'])->name('partnerships.contract-period');
    Route::get('/partnerships/{application}/document/{type}', [AdminController::class, 'viewPartnershipDocument'])->name('partnerships.document');
    Route::post('/partnerships/{application}/upload-document', [AdminController::class, 'uploadPartnershipDocument'])->name('partnerships.upload-document');
    Route::post('/partnerships/{id}/upload-all-documents', [AdminController::class, 'uploadAllPartnershipDocuments'])->name('partnerships.upload.all');
    Route::post('/partnerships/{application}/wizard/approve-loi', [AdminController::class, 'wizardApproveLOI'])->name('partnerships.wizard.approve-loi');
    Route::post('/partnerships/{application}/wizard/reject-loi', [AdminController::class, 'wizardRejectLOI'])->name('partnerships.wizard.reject-loi');
    Route::post('/partnerships/{application}/wizard/approve-form', [AdminController::class, 'wizardApproveForm'])->name('partnerships.wizard.approve-form');
    Route::post('/partnerships/{application}/wizard/reject-form', [AdminController::class, 'wizardRejectForm'])->name('partnerships.wizard.reject-form');
    Route::patch('/partnerships/{application}/wizard/tick-doc', [AdminController::class, 'wizardTickDoc'])->name('partnerships.wizard.tick-doc');
    Route::post('/partnerships/{application}/wizard/final-approve', [AdminController::class, 'wizardFinalApprove'])->name('partnerships.wizard.final-approve');
    Route::delete('/partnerships/{application}', [AdminController::class, 'destroyPartnership'])->name('partnerships.destroy');
});

// Concessionaire Panel (protected)
Route::middleware(['restrict.admin.panel', 'concessionaire'])->prefix('concessionaire')->name('concessionaire.')->group(function () {
    Route::get('/', [ConcessionaireController::class, 'dashboard'])->name('dashboard');
    Route::get('/edit', [ConcessionaireController::class, 'editProfile'])->name('edit');
    Route::patch('/update', [ConcessionaireController::class, 'updateProfile'])->name('update');
    Route::get('/reviews', [ConcessionaireController::class, 'reviews'])->name('reviews');
    Route::get('/products', [ConcessionaireController::class, 'products'])->name('products');
    Route::post('/products', [ConcessionaireController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ConcessionaireController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ConcessionaireController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ConcessionaireController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product}/toggle', [ConcessionaireController::class, 'toggleAvailability'])->name('products.toggle');
    Route::get('/photos', [ConcessionaireController::class, 'photos'])->name('photos');
    Route::post('/carousel-image', [ConcessionaireController::class, 'updateCarouselImage'])->name('carousel-image.update');
    Route::delete('/carousel-image', [ConcessionaireController::class, 'deleteCarouselImage'])->name('carousel-image.delete');
    Route::get('/media', [ConcessionaireController::class, 'media'])->name('media');
    Route::get('/payments', [ConcessionaireController::class, 'paymentsIndex'])->name('payments');
    Route::get('/payments/{payment}/receipt', [ConcessionaireController::class, 'downloadReceipt'])->name('payments.receipt');
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});

// Cashier Panel (protected)
Route::middleware(['auth', 'restrict.admin.panel', 'cashier'])->prefix('cashier')->name('cashier.')->group(function () {
    Route::get('/', [CashierController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [CashierController::class, 'dashboard']);
    Route::get('/payments', [CashierController::class, 'paymentsIndex'])->name('payments');
    Route::post('/payments', [CashierController::class, 'storePayment'])->name('payments.store');
    Route::get('/history', [CashierController::class, 'historyIndex'])->name('history');
    Route::get('/payments/history/view', [CashierController::class, 'viewHistoryPdf'])->name('payments.history.view');
    Route::get('/payments/history/pdf', [CashierController::class, 'downloadHistoryPdf'])->name('payments.history.pdf');
    Route::get('/payments/history/csv', [CashierController::class, 'downloadHistoryCsv'])->name('payments.history.csv');
    Route::get('/payments/{concessionaire}/history/view', [CashierController::class, 'viewConcessionaireHistoryPdf'])->name('payments.concessionaire.history.view');
    Route::get('/payments/{concessionaire}/history/pdf', [CashierController::class, 'downloadConcessionaireHistoryPdf'])->name('payments.concessionaire.history.pdf');
    Route::get('/payments/{concessionaire}/history/csv', [CashierController::class, 'downloadConcessionaireHistoryCsv'])->name('payments.concessionaire.history.csv');
    Route::get('/payments/{payment}/receipt', [CashierController::class, 'downloadReceipt'])->name('payments.receipt');
    Route::patch('/partnerships/{application}/contract-period', [CashierController::class, 'saveContractPeriod'])->name('partnerships.contract-period');
});

// Staff Panel (protected)
Route::middleware(['auth', 'faculty'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [FacultyController::class, 'dashboard'])->name('dashboard');
    Route::post('/notifications/mark-read', [FacultyController::class, 'markPaymentsRead'])->name('notifications.mark-read');
    Route::get('/transaction-logs', [FacultyController::class, 'transactionLogsIndex'])->name('transaction-logs');

    Route::get('/uniform-checkout', [FacultyController::class, 'uniformCheckoutIndex'])->name('uniform-checkout');
    Route::post('/uniform-checkout', [FacultyController::class, 'storeUniformCheckout'])->name('uniform-checkout.store');

    Route::get('/stocks', [FacultyController::class, 'stocksIndex'])->name('stocks.index');
    Route::post('/stocks', [FacultyController::class, 'storeStock'])->name('stocks.store');
    Route::patch('/stocks/{stock}', [FacultyController::class, 'updateStock'])->name('stocks.update');
    Route::delete('/stocks/{stock}', [FacultyController::class, 'destroyStock'])->name('stocks.destroy');
    Route::patch('/stocks/{stock}/visibility', [FacultyController::class, 'toggleStockVisibility'])->name('stocks.visibility');

    Route::get('/partnerships', [FacultyController::class, 'partnershipsIndex'])->name('partnerships.index');
    Route::get('/partnerships/{application}/document/{type}', [FacultyController::class, 'viewPartnershipDocument'])->name('partnerships.document');
    Route::post('/partnerships/{application}/wizard/approve-loi', [FacultyController::class, 'wizardApproveLOI'])->name('partnerships.wizard.approve-loi');
    Route::post('/partnerships/{application}/wizard/reject-loi', [FacultyController::class, 'wizardRejectLOI'])->name('partnerships.wizard.reject-loi');
    Route::post('/partnerships/{application}/wizard/approve-form', [FacultyController::class, 'wizardApproveForm'])->name('partnerships.wizard.approve-form');
    Route::post('/partnerships/{application}/wizard/reject-form', [FacultyController::class, 'wizardRejectForm'])->name('partnerships.wizard.reject-form');
    Route::patch('/partnerships/{application}/wizard/tick-doc', [FacultyController::class, 'wizardTickDoc'])->name('partnerships.wizard.tick-doc');
    Route::post('/partnerships/{application}/wizard/final-approve', [FacultyController::class, 'wizardFinalApprove'])->name('partnerships.wizard.final-approve');
    Route::get('/partnerships/{id}', [FacultyController::class, 'partnershipsShow'])->name('partnerships.show');
    Route::post('/partnerships/{id}/recommend', [FacultyController::class, 'submitRecommendation'])->name('partnerships.recommend');
    Route::post('/partnerships/{id}/upload-document', [FacultyController::class, 'uploadDocument'])->name('partnerships.upload-document');

    Route::get('/concessionaires', [FacultyController::class, 'concessionairesIndex'])->name('concessionaires.index');
    Route::patch('/concessionaires/{id}/monthly-fee', [FacultyController::class, 'updateMonthlyFee'])->name('concessionaires.monthly-fee');
    Route::get('/concessionaires/{id}/edit', [FacultyController::class, 'concessionairesEdit'])->name('concessionaires.edit');
    Route::patch('/concessionaires/{id}', [FacultyController::class, 'concessionairesUpdate'])->name('concessionaires.update');

    Route::get('/history', [FacultyController::class, 'history'])->name('history');
});

// Products (public - anyone can browse)
Route::middleware(['restrict.admin.panel'])->prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
});

Route::middleware(['restrict.admin.panel'])->prefix('stocks')->name('stocks.')->group(function () {
    Route::get('/{stock}', [StockController::class, 'show'])->name('show');
});

Route::middleware(['restrict.admin.panel'])->prefix('concessionaires')->name('concessionaires.')->group(function () {
    Route::get('/', [ConcessionaireController::class, 'publicIndex'])->name('index');
    Route::get('/{user}', [ConcessionaireController::class, 'publicShow'])->name('show');
});

Route::middleware(['auth', 'restrict.admin.panel'])->prefix('concessionaires')->name('concessionaires.')->group(function () {
    Route::post('/{user}/review', [ConcessionaireController::class, 'storeReview'])->name('review.store');
    Route::put('/{user}/review', [ConcessionaireController::class, 'updateReview'])->name('review.update');
    Route::delete('/{user}/review', [ConcessionaireController::class, 'deleteReview'])->name('review.delete');
});

// Product Reviews (authenticated users only)
Route::middleware(['auth', 'restrict.admin.panel'])->prefix('products')->name('products.')->group(function () {
    Route::post('/{product}/review', [ProductController::class, 'storeReview'])->name('review.store');
    Route::put('/{product}/review', [ProductController::class, 'updateReview'])->name('review.update');
    Route::delete('/{product}/review', [ProductController::class, 'deleteReview'])->name('review.delete');
});

// DEV ONLY - DELETE BEFORE DEPLOY
Route::middleware(['local.env.only'])->group(function () {
    Route::get('/dev/test-accounts', [DevController::class, 'showForm'])
        ->name('dev.test-accounts');
    Route::post('/dev/test-accounts', [DevController::class, 'createAccount'])
        ->name('dev.test-accounts.store');
});

require __DIR__.'/settings.php';
