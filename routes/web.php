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

        Route::get('/articles', [App\Http\Controllers\Admin\ArticleController::class, 'index'])->name('articles.index');
        Route::get('/articles/create', [App\Http\Controllers\Admin\ArticleController::class, 'create'])->name('articles.create');
        Route::post('/articles', [App\Http\Controllers\Admin\ArticleController::class, 'store'])->name('articles.store');
    });
});
