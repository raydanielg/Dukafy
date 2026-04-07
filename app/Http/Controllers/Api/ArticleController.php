<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with('category')
            ->whereNotNull('published_at')
            ->latest()
            ->paginate($request->get('per_page', 10));

        return response()->json($articles);
    }

    public function show($id)
    {
        $article = Article::with('category')->findOrFail($id);
        
        return response()->json([
            'article' => $article
        ]);
    }
}
