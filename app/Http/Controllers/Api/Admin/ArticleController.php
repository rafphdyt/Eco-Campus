<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ArticleController extends ApiController
{
    public function categories()
    {
        return $this->success(
            'Daftar kategori berhasil diambil',
            Category::orderBy('name')->get(['category_id', 'name'])
        );
    }

    public function index(Request $request)
    {
        $filters = $this->check($request, [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);
        $articles = Article::with('category')
            ->latest('article_id')
            ->paginate((int) ($filters['per_page'] ?? 20));

        return $this->success('Daftar artikel admin berhasil diambil', [
            'articles' => $articles->getCollection()
                ->map(fn (Article $article) => $this->articleData($article, true))
                ->values(),
            'categories' => Category::orderBy('name')->get(['category_id', 'name']),
            'pagination' => $this->paginationData($articles),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->check($request, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,category_id'],
            'author' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'starts_with:http://,https://'],
            'thumbnail_url' => ['nullable', 'url'],
            'published_at' => ['nullable', 'date'],
            'reading_time' => ['nullable', 'integer', 'min:1'],
        ]);
        $article = Article::create($data + ['published_at' => today()])->load('category');

        return $this->success('Artikel ditambahkan', $this->articleData($article, true), 201);
    }

    public function update(Request $request, Article $article)
    {
        $data = $this->check($request, [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'content' => ['sometimes', 'nullable', 'string'],
            'category_id' => ['sometimes', 'required', 'exists:categories,category_id'],
            'author' => ['sometimes', 'required', 'string', 'max:255'],
            'url' => ['sometimes', 'required', 'url', 'starts_with:http://,https://'],
            'thumbnail_url' => ['sometimes', 'nullable', 'url'],
            'published_at' => ['sometimes', 'nullable', 'date'],
            'reading_time' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ]);

        $article->update($data);
        $article->load('category');

        return $this->success('Artikel diperbarui', $this->articleData($article, true));
    }

    public function destroy(Article $article)
    {
        $id = $article->article_id;
        $article->delete();

        return $this->success('Artikel dihapus', ['article_id' => $id]);
    }
}