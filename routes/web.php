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
