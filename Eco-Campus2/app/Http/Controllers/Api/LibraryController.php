<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class LibraryController extends ApiController
{
    // GET /api/library?cat=1
    public function index(Request $request)
    {
        $cat = (int) $request->query('cat', 0);

        $articles = Article::with('category')
            ->when($cat, fn ($q) => $q->where('category_id', $cat))
            ->latest('article_id')->get()
            ->map(fn ($a) => $a->only(['article_id', 'title', 'description', 'author', 'url', 'published_at'])
                + ['category' => $a->category?->name])->all();

        return $this->success('Data library berhasil diambil', [
            'categories' => Category::all(['category_id', 'name'])->all(),
            'articles' => $articles,
        ]);
    }
}