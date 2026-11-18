<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        return view('admin.articles', [
            'articles' => Article::with('category')->orderByDesc('article_id')->get(),
            'categories' => Category::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,category_id'],
            'author' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'starts_with:http://,https://'],
        ]);
        Article::create($data + ['published_at' => today()]);
        return back()->with('success', 'Artikel ditambahkan.');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return back()->with('success', 'Artikel dihapus.');
    }
}