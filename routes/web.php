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

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

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
    });
});
