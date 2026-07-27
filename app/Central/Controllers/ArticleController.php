<?php

namespace App\Central\Controllers;

use App\Central\Models\Article;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('pages.blog-index', compact('articles'));
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)
            ->whereNotNull('published_at')
            ->firstOrFail();

        return view('pages.blog-show', compact('article'));
    }
}
