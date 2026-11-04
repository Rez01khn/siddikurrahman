<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleFilterRequest;
use App\Models\Article;
use App\Models\NewsCategory;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(ArticleFilterRequest $request): View
    {
        return view('news.index', ['articles' => Article::visible()->with('category')->matching($request->validated())->orderBy('sort_order')->orderByDesc('publication_date')->orderByDesc('id')->paginate(9)->withQueryString(),
            'categories' => NewsCategory::where('is_published', true)->orderBy('sort_order')->orderBy('name')->get(),
            'sections' => collect(), 'visibleAnchors' => ['top'], 'solidHeader' => true]);
    }

    public function show(Article $article): View
    {
        abort_unless(Article::visible()->whereKey($article->id)->exists(), 404);

        return view('news.show', ['article' => $article->load(['category', 'tournament']), 'preview' => false,
            'sections' => collect(), 'visibleAnchors' => ['top'], 'solidHeader' => true]);
    }
}
