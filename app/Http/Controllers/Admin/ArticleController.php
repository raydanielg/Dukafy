<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $articles = DB::table('articles')
            ->leftJoin('article_categories', 'article_categories.id', '=', 'articles.category_id')
            ->select('articles.*', 'article_categories.name as category_name')
            ->orderBy('published_at', 'desc')
            ->get();

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = DB::table('article_categories')->orderBy('name')->get();

        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'image' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);

        DB::table('articles')->insert([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'category_id' => $data['category_id'] ?? null,
            'image' => $data['image'] ?? 'article image (not set)',
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'published_at' => $data['published_at'] ?? now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.articles.index');
    }
}
