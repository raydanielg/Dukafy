<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing.landing');
});

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('/privacy', function () {
    return view('landing.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('landing.terms');
})->name('terms');

Route::get('/legal', function () {
    return view('landing.legal');
})->name('legal');

Route::get('/about', function () {
    return view('landing.landing');
})->name('about');

Route::get('/articles', function () {
    $articles = DB::table('articles')->orderBy('published_at', 'desc')->get();
    return view('landing.articles_list', compact('articles'));
})->name('articles');

Route::get('/articles/{slug}', function ($slug) {
    $article = DB::table('articles')->where('slug', $slug)->first();
    if (!$article) abort(404);
    return view('landing.article_details', compact('article'));
})->name('articles.show');

Route::post('/newsletter/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

Route::get('/categories', function () {
    $categories = DB::table('article_categories')
        ->select('article_categories.*', DB::raw('(SELECT COUNT(*) FROM articles WHERE articles.category_id = article_categories.id) as articles_count'))
        ->get();
    return view('landing.categories_list', compact('categories'));
})->name('categories');

Route::get('/articles/category/{slug}', function ($slug) {
    $category = DB::table('article_categories')->where('slug', $slug)->first();
    if (!$category) abort(404);
    $articles = DB::table('articles')->where('category_id', $category->id)->orderBy('published_at', 'desc')->get();
    return view('landing.articles_list', compact('articles', 'category'));
})->name('articles.category');

Auth::routes();

Route::get('/home', [App\Http\Controllers\User\DashboardController::class, 'index'])->name('home');

Route::middleware(['auth', 'admin', 'admin.activity'])->group(function () {
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::post('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/delete', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/users/pending', [App\Http\Controllers\Admin\UserController::class, 'pending'])->name('users.pending');
        Route::get('/users/banned', [App\Http\Controllers\Admin\UserController::class, 'banned'])->name('users.banned');
        Route::post('/users/{id}/approve', [App\Http\Controllers\Admin\UserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{id}/ban', [App\Http\Controllers\Admin\UserController::class, 'ban'])->name('users.ban');
        Route::post('/users/{id}/unban', [App\Http\Controllers\Admin\UserController::class, 'unban'])->name('users.unban');

        Route::get('/roles', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');

        Route::get('/permissions', [App\Http\Controllers\Admin\PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permissions', [App\Http\Controllers\Admin\PermissionController::class, 'update'])->name('permissions.update');

        Route::get('/groups', [App\Http\Controllers\Admin\UserGroupController::class, 'index'])->name('groups.index');
        Route::get('/groups/create', [App\Http\Controllers\Admin\UserGroupController::class, 'create'])->name('groups.create');
        Route::post('/groups', [App\Http\Controllers\Admin\UserGroupController::class, 'store'])->name('groups.store');

        Route::get('/activity', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity.index');
        Route::get('/login-history', [App\Http\Controllers\Admin\LoginHistoryController::class, 'index'])->name('login_history.index');

        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/login-security', [App\Http\Controllers\Admin\SecurityController::class, 'loginSecurity'])->name('login_security');
            Route::post('/login-security', [App\Http\Controllers\Admin\SecurityController::class, 'updateLoginSecurity'])->name('login_security.update');

            Route::get('/password-policy', [App\Http\Controllers\Admin\SecurityController::class, 'passwordPolicy'])->name('password_policy');
            Route::post('/password-policy', [App\Http\Controllers\Admin\SecurityController::class, 'updatePasswordPolicy'])->name('password_policy.update');

            Route::get('/ip-whitelisting', [App\Http\Controllers\Admin\SecurityController::class, 'ipWhitelisting'])->name('ip_whitelisting');
            Route::post('/ip-whitelisting', [App\Http\Controllers\Admin\SecurityController::class, 'addIpWhitelist'])->name('ip_whitelisting.add');

            Route::get('/blocked-ips', [App\Http\Controllers\Admin\SecurityController::class, 'blockedIps'])->name('blocked_ips');
            Route::post('/blocked-ips', [App\Http\Controllers\Admin\SecurityController::class, 'blockIp'])->name('blocked_ips.add');

            Route::get('/database-encryption', [App\Http\Controllers\Admin\SecurityController::class, 'databaseEncryption'])->name('database_encryption');
            Route::post('/database-encryption', [App\Http\Controllers\Admin\SecurityController::class, 'updateDatabaseEncryption'])->name('database_encryption.update');

            Route::get('/backup-security', [App\Http\Controllers\Admin\SecurityController::class, 'backupSecurity'])->name('backup_security');
            Route::post('/backup-security', [App\Http\Controllers\Admin\SecurityController::class, 'updateBackupSecurity'])->name('backup_security.update');

            Route::get('/audit-log', [App\Http\Controllers\Admin\SecurityController::class, 'auditLog'])->name('audit_log');
            Route::get('/session-management', [App\Http\Controllers\Admin\SecurityController::class, 'sessionManagement'])->name('session_management');

            Route::get('/api-security', [App\Http\Controllers\Admin\SecurityController::class, 'apiSecurity'])->name('api_security');
            Route::post('/api-security/base-url', [App\Http\Controllers\Admin\SecurityController::class, 'updateApiBaseUrl'])->name('api_security.base_url');
            Route::post('/api-security', [App\Http\Controllers\Admin\SecurityController::class, 'createApiKey'])->name('api_security.create');
            Route::post('/api-security/{id}/revoke', [App\Http\Controllers\Admin\SecurityController::class, 'revokeApiKey'])->name('api_security.revoke');

            Route::get('/data-retention', [App\Http\Controllers\Admin\SecurityController::class, 'dataRetentionPolicy'])->name('data_retention');
            Route::post('/data-retention', [App\Http\Controllers\Admin\SecurityController::class, 'updateDataRetentionPolicy'])->name('data_retention.update');

            Route::get('/security-alerts', [App\Http\Controllers\Admin\SecurityController::class, 'securityAlerts'])->name('security_alerts');
            Route::post('/security-alerts', [App\Http\Controllers\Admin\SecurityController::class, 'updateSecurityAlerts'])->name('security_alerts.update');
        });

        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('/subscriptions', [App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('subscriptions');
            Route::get('/history', [App\Http\Controllers\Admin\SubscriptionController::class, 'history'])->name('history');
            Route::get('/expiring-soon', [App\Http\Controllers\Admin\SubscriptionController::class, 'expiringSoon'])->name('expiring');
            Route::get('/cancelled', [App\Http\Controllers\Admin\SubscriptionController::class, 'cancelled'])->name('cancelled');
            Route::get('/trial-requests', [App\Http\Controllers\Admin\SubscriptionController::class, 'trialRequests'])->name('trials');
            Route::post('/trial-requests/{id}/approve', [App\Http\Controllers\Admin\SubscriptionController::class, 'approveTrial'])->name('trials.approve');
            Route::post('/trial-requests/{id}/reject', [App\Http\Controllers\Admin\SubscriptionController::class, 'rejectTrial'])->name('trials.reject');
            Route::get('/invoices-payments', [App\Http\Controllers\Admin\SubscriptionController::class, 'invoices'])->name('billing');
            Route::post('/invoices-payments/payment', [App\Http\Controllers\Admin\SubscriptionController::class, 'addPayment'])->name('billing.payment');

            Route::get('/plans', [App\Http\Controllers\Admin\PlanController::class, 'index'])->name('plans');
            Route::get('/plans/create', [App\Http\Controllers\Admin\PlanController::class, 'create'])->name('plans.create');
            Route::post('/plans', [App\Http\Controllers\Admin\PlanController::class, 'store'])->name('plans.store');
            Route::get('/plans/{id}/edit', [App\Http\Controllers\Admin\PlanController::class, 'edit'])->name('plans.edit');
            Route::post('/plans/{id}', [App\Http\Controllers\Admin\PlanController::class, 'update'])->name('plans.update');

            Route::get('/assign', [App\Http\Controllers\Admin\SubscriptionAssignController::class, 'create'])->name('assign');
            Route::post('/assign', [App\Http\Controllers\Admin\SubscriptionAssignController::class, 'store'])->name('assign.store');
        });

        Route::prefix('finance')->name('finance.')->group(function () {
            Route::get('/revenue-overview', [App\Http\Controllers\Admin\FinanceController::class, 'revenueOverview'])->name('revenue_overview');
            Route::get('/payment-methods', [App\Http\Controllers\Admin\FinanceController::class, 'paymentMethods'])->name('payment_methods');
            Route::post('/payment-methods', [App\Http\Controllers\Admin\FinanceController::class, 'addPaymentMethod'])->name('payment_methods.store');
            Route::get('/payment-gateway', [App\Http\Controllers\Admin\FinanceController::class, 'paymentGateway'])->name('payment_gateway');
            Route::post('/payment-gateway', [App\Http\Controllers\Admin\FinanceController::class, 'updatePaymentGateway'])->name('payment_gateway.update');
            Route::get('/invoice-settings', [App\Http\Controllers\Admin\FinanceController::class, 'invoiceSettings'])->name('invoice_settings');
            Route::post('/invoice-settings', [App\Http\Controllers\Admin\FinanceController::class, 'updateInvoiceSettings'])->name('invoice_settings.update');
            Route::get('/tax-settings', [App\Http\Controllers\Admin\FinanceController::class, 'taxSettings'])->name('tax_settings');
            Route::post('/tax-settings', [App\Http\Controllers\Admin\FinanceController::class, 'updateTaxSettings'])->name('tax_settings.update');
            Route::get('/expenses', [App\Http\Controllers\Admin\FinanceController::class, 'expenses'])->name('expenses');
            Route::post('/expenses', [App\Http\Controllers\Admin\FinanceController::class, 'addExpense'])->name('expenses.store');
            Route::get('/profit-loss', [App\Http\Controllers\Admin\FinanceController::class, 'profitLoss'])->name('profit_loss');
        });

        Route::get('/articles', [App\Http\Controllers\Admin\ArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/create', [App\Http\Controllers\Admin\ArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles', [App\Http\Controllers\Admin\ArticleController::class, 'store'])->name('articles.store');
        Route::get('/articles/{id}/edit', [App\Http\Controllers\Admin\ArticleController::class, 'edit'])->name('articles.edit');
        Route::post('/articles/{id}', [App\Http\Controllers\Admin\ArticleController::class, 'update'])->name('articles.update');
        Route::post('/articles/{id}/delete', [App\Http\Controllers\Admin\ArticleController::class, 'destroy'])->name('articles.destroy');

        Route::get('/article-categories', [App\Http\Controllers\Admin\ArticleCategoryController::class, 'index'])->name('article_categories.index');
        Route::get('/article-categories/create', [App\Http\Controllers\Admin\ArticleCategoryController::class, 'create'])->name('article_categories.create');
        Route::post('/article-categories', [App\Http\Controllers\Admin\ArticleCategoryController::class, 'store'])->name('article_categories.store');
        Route::get('/article-categories/{id}/edit', [App\Http\Controllers\Admin\ArticleCategoryController::class, 'edit'])->name('article_categories.edit');
        Route::post('/article-categories/{id}', [App\Http\Controllers\Admin\ArticleCategoryController::class, 'update'])->name('article_categories.update');
        Route::post('/article-categories/{id}/delete', [App\Http\Controllers\Admin\ArticleCategoryController::class, 'destroy'])->name('article_categories.destroy');

        Route::prefix('business-data')->name('business_data.')->group(function () {
            Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
            Route::get('/products/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('products.create');
            Route::post('/products', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('products.store');
            Route::get('/products/{id}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('products.edit');
            Route::post('/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('products.update');
            Route::post('/products/{id}/delete', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('products.destroy');

            Route::get('/product-categories', [App\Http\Controllers\Admin\ProductCategoryController::class, 'index'])->name('product_categories.index');
            Route::get('/product-categories/create', [App\Http\Controllers\Admin\ProductCategoryController::class, 'create'])->name('product_categories.create');
            Route::post('/product-categories', [App\Http\Controllers\Admin\ProductCategoryController::class, 'store'])->name('product_categories.store');
            Route::get('/product-categories/{id}/edit', [App\Http\Controllers\Admin\ProductCategoryController::class, 'edit'])->name('product_categories.edit');
            Route::post('/product-categories/{id}', [App\Http\Controllers\Admin\ProductCategoryController::class, 'update'])->name('product_categories.update');
            Route::post('/product-categories/{id}/delete', [App\Http\Controllers\Admin\ProductCategoryController::class, 'destroy'])->name('product_categories.destroy');

            Route::get('/low-stock-alerts', [App\Http\Controllers\Admin\BusinessDataController::class, 'lowStockAlerts'])->name('low_stock_alerts');

            Route::get('/bulk-import-export', [App\Http\Controllers\Admin\BusinessDataController::class, 'bulkImportExport'])->name('bulk_import_export');
            Route::get('/bulk-import-export/products/export', [App\Http\Controllers\Admin\BusinessDataController::class, 'exportProductsCsv'])->name('bulk_import_export.products.export');
            Route::post('/bulk-import-export/products/import', [App\Http\Controllers\Admin\BusinessDataController::class, 'importProductsCsv'])->name('bulk_import_export.products.import');

            Route::get('/sales', [App\Http\Controllers\Admin\SaleController::class, 'index'])->name('sales.index');
            Route::get('/sales/create', [App\Http\Controllers\Admin\SaleController::class, 'create'])->name('sales.create');
            Route::post('/sales', [App\Http\Controllers\Admin\SaleController::class, 'store'])->name('sales.store');
            Route::get('/sales/{id}', [App\Http\Controllers\Admin\SaleController::class, 'show'])->name('sales.show');
            Route::post('/sales/{id}/delete', [App\Http\Controllers\Admin\SaleController::class, 'destroy'])->name('sales.destroy');

            Route::get('/sales-by-business', [App\Http\Controllers\Admin\SaleController::class, 'salesByBusiness'])->name('sales_by_business');
            Route::get('/sales-by-user', [App\Http\Controllers\Admin\SaleController::class, 'salesByUser'])->name('sales_by_user');

            Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
            Route::get('/customers/create', [App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('customers.create');
            Route::post('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('customers.store');
            Route::get('/customers/{id}/edit', [App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('customers.edit');
            Route::post('/customers/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('customers.update');
            Route::post('/customers/{id}/delete', [App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('customers.destroy');

            Route::get('/customer-groups', [App\Http\Controllers\Admin\CustomerGroupController::class, 'index'])->name('customer_groups.index');
            Route::get('/customer-groups/create', [App\Http\Controllers\Admin\CustomerGroupController::class, 'create'])->name('customer_groups.create');
            Route::post('/customer-groups', [App\Http\Controllers\Admin\CustomerGroupController::class, 'store'])->name('customer_groups.store');
            Route::get('/customer-groups/{id}/edit', [App\Http\Controllers\Admin\CustomerGroupController::class, 'edit'])->name('customer_groups.edit');
            Route::post('/customer-groups/{id}', [App\Http\Controllers\Admin\CustomerGroupController::class, 'update'])->name('customer_groups.update');
            Route::post('/customer-groups/{id}/delete', [App\Http\Controllers\Admin\CustomerGroupController::class, 'destroy'])->name('customer_groups.destroy');

            Route::get('/blacklisted-customers', [App\Http\Controllers\Admin\CustomerController::class, 'blacklisted'])->name('blacklisted_customers');
        });

        Route::prefix('system-settings')->name('system_settings.')->group(function () {
            Route::get('/general', [App\Http\Controllers\Admin\SystemSettingsController::class, 'general'])->name('general');
            Route::post('/general', [App\Http\Controllers\Admin\SystemSettingsController::class, 'updateGeneral'])->name('general.update');

            Route::get('/business-defaults', [App\Http\Controllers\Admin\SystemSettingsController::class, 'businessDefaults'])->name('business_defaults');
            Route::post('/business-defaults', [App\Http\Controllers\Admin\SystemSettingsController::class, 'updateBusinessDefaults'])->name('business_defaults.update');

            Route::get('/email', [App\Http\Controllers\Admin\SystemSettingsController::class, 'email'])->name('email');
            Route::post('/email', [App\Http\Controllers\Admin\SystemSettingsController::class, 'updateEmail'])->name('email.update');

            Route::get('/sms-whatsapp', [App\Http\Controllers\Admin\SystemSettingsController::class, 'smsWhatsapp'])->name('sms_whatsapp');
            Route::post('/sms-whatsapp', [App\Http\Controllers\Admin\SystemSettingsController::class, 'updateSmsWhatsapp'])->name('sms_whatsapp.update');

            Route::get('/backup', [App\Http\Controllers\Admin\SystemSettingsController::class, 'backup'])->name('backup');
            Route::post('/backup', [App\Http\Controllers\Admin\SystemSettingsController::class, 'updateBackup'])->name('backup.update');

            Route::get('/maintenance', [App\Http\Controllers\Admin\SystemSettingsController::class, 'maintenance'])->name('maintenance');
            Route::post('/maintenance', [App\Http\Controllers\Admin\SystemSettingsController::class, 'updateMaintenance'])->name('maintenance.update');

            Route::post('/clear-cache', [App\Http\Controllers\Admin\SystemSettingsController::class, 'clearCache'])->name('clear_cache');
            Route::get('/health', [App\Http\Controllers\Admin\SystemSettingsController::class, 'health'])->name('health');
        });

        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/access', [App\Http\Controllers\Admin\LogsController::class, 'access'])->name('access');
            Route::get('/errors', [App\Http\Controllers\Admin\LogsController::class, 'errors'])->name('errors');
            Route::get('/payments', [App\Http\Controllers\Admin\LogsController::class, 'payments'])->name('payments');
            Route::get('/emails', [App\Http\Controllers\Admin\LogsController::class, 'emails'])->name('emails');
        });

        Route::prefix('extras')->name('extras.')->group(function () {
            Route::get('/modules', [App\Http\Controllers\Admin\ExtrasController::class, 'modulesIndex'])->name('modules.index');
            Route::get('/modules/create', [App\Http\Controllers\Admin\ExtrasController::class, 'modulesCreate'])->name('modules.create');
            Route::post('/modules', [App\Http\Controllers\Admin\ExtrasController::class, 'modulesStore'])->name('modules.store');
            Route::get('/modules/{id}/edit', [App\Http\Controllers\Admin\ExtrasController::class, 'modulesEdit'])->name('modules.edit');
            Route::post('/modules/{id}', [App\Http\Controllers\Admin\ExtrasController::class, 'modulesUpdate'])->name('modules.update');
            Route::post('/modules/{id}/delete', [App\Http\Controllers\Admin\ExtrasController::class, 'modulesDestroy'])->name('modules.destroy');

            Route::get('/announcements', [App\Http\Controllers\Admin\ExtrasController::class, 'announcementsIndex'])->name('announcements.index');
            Route::get('/announcements/create', [App\Http\Controllers\Admin\ExtrasController::class, 'announcementsCreate'])->name('announcements.create');
            Route::post('/announcements', [App\Http\Controllers\Admin\ExtrasController::class, 'announcementsStore'])->name('announcements.store');
            Route::get('/announcements/{id}/edit', [App\Http\Controllers\Admin\ExtrasController::class, 'announcementsEdit'])->name('announcements.edit');
            Route::post('/announcements/{id}', [App\Http\Controllers\Admin\ExtrasController::class, 'announcementsUpdate'])->name('announcements.update');
            Route::post('/announcements/{id}/delete', [App\Http\Controllers\Admin\ExtrasController::class, 'announcementsDestroy'])->name('announcements.destroy');

            Route::get('/system-logs', [App\Http\Controllers\Admin\ExtrasController::class, 'systemLogs'])->name('system_logs');

            Route::get('/cron-jobs', [App\Http\Controllers\Admin\ExtrasController::class, 'cronJobsIndex'])->name('cron_jobs.index');
            Route::get('/cron-jobs/create', [App\Http\Controllers\Admin\ExtrasController::class, 'cronJobsCreate'])->name('cron_jobs.create');
            Route::post('/cron-jobs', [App\Http\Controllers\Admin\ExtrasController::class, 'cronJobsStore'])->name('cron_jobs.store');
            Route::get('/cron-jobs/{id}/edit', [App\Http\Controllers\Admin\ExtrasController::class, 'cronJobsEdit'])->name('cron_jobs.edit');
            Route::post('/cron-jobs/{id}', [App\Http\Controllers\Admin\ExtrasController::class, 'cronJobsUpdate'])->name('cron_jobs.update');
            Route::post('/cron-jobs/{id}/delete', [App\Http\Controllers\Admin\ExtrasController::class, 'cronJobsDestroy'])->name('cron_jobs.destroy');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/system-usage', [App\Http\Controllers\Admin\ReportController::class, 'systemUsage'])->name('system_usage');
            Route::get('/business-performance', [App\Http\Controllers\Admin\ReportController::class, 'businessPerformance'])->name('business_performance');
            Route::get('/subscription-revenue', [App\Http\Controllers\Admin\ReportController::class, 'subscriptionRevenue'])->name('subscription_revenue');
            Route::get('/churn-report', [App\Http\Controllers\Admin\ReportController::class, 'churnReport'])->name('churn_report');
        });

        Route::prefix('help')->name('help.')->group(function () {
            Route::get('/documentation', [App\Http\Controllers\Admin\HelpSupportController::class, 'documentation'])->name('documentation');
            Route::get('/tickets', [App\Http\Controllers\Admin\HelpSupportController::class, 'tickets'])->name('tickets');
            Route::get('/tickets/{id}', [App\Http\Controllers\Admin\HelpSupportController::class, 'ticketShow'])->name('tickets.show');
            Route::post('/tickets/{id}/reply', [App\Http\Controllers\Admin\HelpSupportController::class, 'ticketReply'])->name('tickets.reply');
            Route::get('/system-info', [App\Http\Controllers\Admin\HelpSupportController::class, 'systemInfo'])->name('system_info');
            Route::get('/contact-developer', [App\Http\Controllers\Admin\HelpSupportController::class, 'contactDeveloper'])->name('contact_developer');
        });
    });
});
