<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);
        Route::get('/managers/search', [App\Http\Controllers\Api\AuthController::class, 'searchManagers']);
        Route::post('/approve-initial', [App\Http\Controllers\Api\AuthController::class, 'approveInitial']);
        Route::post('/complete-onboarding', [App\Http\Controllers\Api\AuthController::class, 'completeOnboarding']);
        Route::delete('/delete-account', [App\Http\Controllers\Api\AuthController::class, 'deleteAccount']);
        Route::post('/update-profile', [App\Http\Controllers\Api\AuthController::class, 'updateProfile']);
        Route::post('/update-business-logo', [App\Http\Controllers\Api\AuthController::class, 'updateBusinessLogo']);
        Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);

        // Articles
        Route::get('/articles', [App\Http\Controllers\Api\ArticleController::class, 'index']);
        Route::get('/articles/{id}', [App\Http\Controllers\Api\ArticleController::class, 'show']);

        // POS
        Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
        Route::get('/customers', [App\Http\Controllers\Api\CustomerController::class, 'index']);

        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Api\DashboardController::class, 'index']);
        Route::get('/dashboard/stats', [App\Http\Controllers\Api\DashboardController::class, 'stats']);
        Route::get('/dashboard/summary', [App\Http\Controllers\Api\DashboardController::class, 'summary']);
        Route::get('/sales/today', [App\Http\Controllers\Api\DashboardController::class, 'todaySales']);
        Route::get('/inventory/value', [App\Http\Controllers\Api\DashboardController::class, 'inventoryValue']);
        Route::get('/credits/outstanding', [App\Http\Controllers\Api\DashboardController::class, 'outstandingCredits']);
        Route::get('/expenses/total', [App\Http\Controllers\Api\DashboardController::class, 'totalExpenses']);

        // Products - NOTE: stats must come before {id} routes!
        Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
        Route::post('/products', [App\Http\Controllers\Api\ProductController::class, 'store']);
        Route::get('/products/stats', [App\Http\Controllers\Api\ProductController::class, 'stats']);
        Route::get('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'show']);
        Route::put('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'update']);
        Route::delete('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'destroy']);

        // Product Categories
        Route::get('/product-categories', [App\Http\Controllers\Api\ProductCategoryController::class, 'index']);
        Route::post('/product-categories', [App\Http\Controllers\Api\ProductCategoryController::class, 'store']);

        // Customers - NOTE: stats must come before {id} routes!
        Route::get('/customers', [App\Http\Controllers\Api\CustomerController::class, 'index']);
        Route::post('/customers', [App\Http\Controllers\Api\CustomerController::class, 'store']);
        Route::get('/customers/stats', [App\Http\Controllers\Api\CustomerController::class, 'stats']);
        Route::get('/customers/{id}', [App\Http\Controllers\Api\CustomerController::class, 'show']);
        Route::put('/customers/{id}', [App\Http\Controllers\Api\CustomerController::class, 'update']);
        Route::delete('/customers/{id}', [App\Http\Controllers\Api\CustomerController::class, 'destroy']);

        // Customer Groups
        Route::get('/customer-groups', [App\Http\Controllers\Api\CustomerGroupController::class, 'index']);

        // Sales
        Route::get('/sales', [App\Http\Controllers\Api\SaleController::class, 'index']);
        Route::post('/sales', [App\Http\Controllers\Api\SaleController::class, 'store']);
        Route::get('/sales/{id}', [App\Http\Controllers\Api\SaleController::class, 'show']);
        Route::get('/sales/summary', [App\Http\Controllers\Api\SaleController::class, 'summary']);

        // Suppliers
        Route::get('/suppliers', [App\Http\Controllers\Api\SupplierController::class, 'index']);
        Route::post('/suppliers', [App\Http\Controllers\Api\SupplierController::class, 'store']);

        // Expenses
        Route::get('/expenses', [App\Http\Controllers\Api\ExpenseController::class, 'index']);
        Route::post('/expenses', [App\Http\Controllers\Api\ExpenseController::class, 'store']);
        Route::get('/expenses/stats', [App\Http\Controllers\Api\ExpenseController::class, 'stats']);

        // Payments
        Route::get('/payments', [App\Http\Controllers\Api\PaymentController::class, 'index']);
        Route::post('/payments', [App\Http\Controllers\Api\PaymentController::class, 'store']);
        Route::get('/payments/recent', [App\Http\Controllers\Api\PaymentController::class, 'recent']);

        // Reports
        Route::get('/reports/sales', [App\Http\Controllers\Api\ReportController::class, 'sales']);
        Route::get('/reports/profit', [App\Http\Controllers\Api\ReportController::class, 'profit']);
        Route::get('/reports/inventory', [App\Http\Controllers\Api\ReportController::class, 'inventory']);

        // Inventory
        Route::get('/inventory', [App\Http\Controllers\Api\InventoryController::class, 'index']);
        Route::post('/inventory/adjust', [App\Http\Controllers\Api\InventoryController::class, 'adjust']);

        // Banking
        Route::get('/banking/accounts', [App\Http\Controllers\Api\BankingController::class, 'accounts']);
        Route::get('/banking/transactions', [App\Http\Controllers\Api\BankingController::class, 'transactions']);

        // Team/Members
        Route::get('/employees', [App\Http\Controllers\Api\EmployeeController::class, 'index']);

        // Shops/Businesses
        Route::get('/shops', [App\Http\Controllers\Api\ShopController::class, 'index']);

        // Invoices
        Route::get('/invoices', [App\Http\Controllers\Api\InvoiceController::class, 'index']);
    });
});
