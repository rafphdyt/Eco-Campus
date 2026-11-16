<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class LibraryController extends ApiController
{
    public function index(Request $request)
    {
        $filters = $this->check($request, [
            'category_id' => ['sometimes', 'integer', 'exists:categories,category_id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);
        $perPage = (int) ($filters['per_page'] ?? 15);

        $articles = Article::with('category')
            ->when(isset($filters['category_id']), fn ($query) => $query->where('category_id', $filters['category_id']))
            ->latest('article_id')
            ->paginate($perPage);

        return $this->success('Pustaka berhasil diambil', [
            'categories' => Category::orderBy('name')->get(['category_id', 'name']),
            'articles' => $articles->getCollection()
                ->map(fn (Article $article) => $this->articleData($article))
                ->values(),
            'pagination' => $this->paginationData($articles),
            'selected_category_id' => $filters['category_id'] ?? null,
        ]);
    }

    public function show(Article $article)
    {
        $article->load('category');

        return $this->success('Artikel berhasil diambil', $this->articleData($article, true));
    }
}