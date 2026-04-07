<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $categories = DB::table('article_categories')->orderBy('name')->get();

        return view('admin.articles.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.articles.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        DB::table('article_categories')->updateOrInsert(
            ['slug' => $data['slug']],
            [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'icon' => $data['icon'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('admin.article_categories.index');
    }

    public function edit(int $id)
    {
        $category = DB::table('article_categories')->where('id', $id)->first();
        abort_if(!$category, 404);

        return view('admin.articles.categories.edit', compact('category'));
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
        ]);

        DB::table('article_categories')->where('id', $id)->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.article_categories.edit', $id);
    }

    public function destroy(int $id)
    {
        DB::table('article_categories')->where('id', $id)->delete();

        return redirect()->route('admin.article_categories.index');
    }
}
