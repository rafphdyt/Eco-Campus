<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Challenge;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'challenges' => Challenge::available()->get(),
            'doneIds' => auth()->user()->doneTodayIds(),
            'articles' => Article::with('category')->latest('article_id')->take(5)->get(),
        ]);
    }
}