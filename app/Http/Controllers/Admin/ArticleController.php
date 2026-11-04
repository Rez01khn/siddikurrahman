<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveArticleRequest;
use App\Http\Requests\ArticleFilterRequest;
use App\Models\Article;
use App\Models\NewsCategory;
use App\Models\Tournament;
use App\Services\CareerContentWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class ArticleController extends Controller
{
    public function index(ArticleFilterRequest $request): View
    {
        $query = Article::with('category')->matching($request->validated());
        if ($status = $request->validated('status')) {
            $query->where('status', $status);
        }

        return view('admin.news.index', ['title' => 'News & Articles', 'articles' => $query->orderBy('sort_order')->orderByDesc('publication_date')->orderByDesc('id')->paginate(15)->withQueryString(), 'categories' => NewsCategory::orderBy('sort_order')->orderBy('name')->get()]);
    }

    private function form(Article $article): View
    {
        return view('admin.news.form', ['title' => $article->exists ? 'Edit Article' : 'Create Article', 'article' => $article,
            'categories' => NewsCategory::orderBy('sort_order')->orderBy('name')->get(),
            'tournaments' => Tournament::orderByDesc('start_date')->get(['id', 'name', 'start_date'])]);
    }

    public function create(): View
    {
        return $this->form(new Article(['status' => 'draft', 'sort_order' => 0]));
    }

    public function edit(Article $article): View
    {
        return $this->form($article);
    }

    public function store(SaveArticleRequest $request, CareerContentWriter $writer): RedirectResponse
    {
        return $this->persist($request, new Article, $writer);
    }

    public function update(SaveArticleRequest $request, Article $article, CareerContentWriter $writer): RedirectResponse
    {
        return $this->persist($request, $article, $writer);
    }

    private function persist(SaveArticleRequest $request, Article $article, CareerContentWriter $writer): RedirectResponse
    {
        try {
            $cleaned = $writer->save($article, $request->safe()->except(['image', 'remove_image']), $request, ['image'], 'articles');
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput($request->except('image'))->withErrors(['save' => 'The article could not be saved. Existing content and images were kept.']);
        }

        return redirect()->route('admin.news.index')->with('status', 'Article saved.')->with('warning', $cleaned ? null : 'Saved, but an unused image could not be removed.');
    }

    public function preview(Article $article): View
    {
        return view('news.show', ['article' => $article->load(['category', 'tournament']), 'preview' => true,
            'sections' => collect(), 'visibleAnchors' => ['top'], 'solidHeader' => true]);
    }

    public function destroy(Article $article, CareerContentWriter $writer): RedirectResponse
    {
        try {
            $cleaned = $writer->delete($article, ['image']);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['delete' => 'The article could not be deleted. Please try again.']);
        }

        return redirect()->route('admin.news.index')->with('status','Article deleted.')->with('warning',$cleaned ? null : 'Deleted, but an unused image could not be removed.');
    }
}
