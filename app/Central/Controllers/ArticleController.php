<?php

namespace App\Central\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = \App\Central\Models\Article::whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('pages.blog-index', compact('articles'));
    }

    public function show($slug)
    {
        $article = \App\Central\Models\Article::where('slug', $slug)
            ->whereNotNull('published_at')
            ->firstOrFail();

        return view('pages.blog-show', compact('article'));
    }
}
