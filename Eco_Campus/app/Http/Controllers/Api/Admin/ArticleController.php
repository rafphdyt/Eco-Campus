<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends ApiController
{
    // GET /api/admin/articles
    public function index()
    {
        $data = Article::with('category')->orderByDesc('article_id')->get()
            ->map(fn ($a) => $a->only(['article_id', 'title', 'description', 'author', 'url', 'published_at'])
                + ['category' => $a->category?->name])->all();

        return $this->success('Data artikel berhasil diambil', $data);
    }

    // POST /api/admin/articles
    public function store(Request $request)
    {
        $data = $this->check($request, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,category_id'],
            'author' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'starts_with:http://,https://'],
        ], [
            'title.required' => 'Judul wajib diisi',
            'category_id.required' => 'Kategori wajib dipilih',
            'category_id.exists' => 'Kategori tidak ditemukan',
            'author.required' => 'Penulis wajib diisi',
            'url.required' => 'URL wajib diisi',
            'url.url' => 'Format URL tidak valid',
            'url.starts_with' => 'URL harus diawali http:// atau https://',
        ]);

        $a = Article::create($data + ['published_at' => today()]);

        return $this->success('Artikel ditambahkan',
            $a->only(['article_id', 'title', 'description', 'author', 'url', 'category_id']), 201);
    }

    // DELETE /api/admin/articles/{article}
    public function destroy(Article $article)
    {
        $id = $article->article_id;
        $article->delete();

        return $this->success('Artikel dihapus', ['article_id' => $id]);
    }
}