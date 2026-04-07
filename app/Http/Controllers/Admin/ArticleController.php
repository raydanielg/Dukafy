<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            ->paginate(12);

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
            'image_file' => ['nullable', 'file', 'image', 'max:5120'],
            'age_range' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['nullable'],
        ]);

        $imagePath = $data['image'] ?? null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('articles', 'public');
        }

        $categoryName = null;
        if (!empty($data['category_id'])) {
            $categoryName = DB::table('article_categories')->where('id', $data['category_id'])->value('name');
        }

        DB::table('articles')->insert([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'category_id' => $data['category_id'] ?? null,
            'category' => $categoryName,
            'age_range' => $data['age_range'] ?? null,
            'image' => $imagePath,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'published_at' => $data['published_at'] ?? now(),
            'is_featured' => isset($data['is_featured']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.articles.index');
    }

    public function edit(int $id)
    {
        $article = DB::table('articles')->where('id', $id)->first();
        abort_if(!$article, 404);

        $categories = DB::table('article_categories')->orderBy('name')->get();

        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, int $id)
    {
        $article = DB::table('articles')->where('id', $id)->first();
        abort_if(!$article, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'file', 'image', 'max:5120'],
            'age_range' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['nullable'],
        ]);

        $imagePath = $data['image'] ?? $article->image;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('articles', 'public');
        }

        $categoryName = null;
        if (!empty($data['category_id'])) {
            $categoryName = DB::table('article_categories')->where('id', $data['category_id'])->value('name');
        }

        DB::table('articles')->where('id', $id)->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'category_id' => $data['category_id'] ?? null,
            'category' => $categoryName,
            'age_range' => $data['age_range'] ?? null,
            'image' => $imagePath,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'published_at' => $data['published_at'] ?? null,
            'is_featured' => isset($data['is_featured']),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.articles.edit', $id);
    }

    public function destroy(int $id)
    {
        DB::table('articles')->where('id', $id)->delete();
        return redirect()->route('admin.articles.index');
    }
}
