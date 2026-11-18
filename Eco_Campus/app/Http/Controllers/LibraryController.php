<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $cat = (int) $request->query('cat', 0);

        $articles = Article::with('category')
            ->when($cat, fn ($q) => $q->where('category_id', $cat))
            ->latest('article_id')->get();

        return view('library', [
            'articles' => $articles,
            'categories' => Category::all(),
            'cat' => $cat,
        ]);
    }
}