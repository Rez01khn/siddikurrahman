<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveNewsCategoryRequest;
use App\Models\NewsCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $query = NewsCategory::withCount('articles');
        if (! empty($filters['q'])) {
            $query->where('name', 'like', '%'.$filters['q'].'%');
        }

        return view('admin.news.categories.index', ['title' => 'News Categories', 'categories' => $query->orderBy('sort_order')->orderBy('id')->paginate(15)->withQueryString()]);
    }

    public function create(): View
    {
        return $this->form(new NewsCategory(['sort_order' => 0]));
    }

    public function edit(NewsCategory $news_category): View
    {
        return $this->form($news_category);
    }

    private function form(NewsCategory $category): View
    {
        return view('admin.news.categories.form', ['title' => 'News Category', 'category' => $category]);
    }

    public function store(SaveNewsCategoryRequest $request): RedirectResponse
    {
        NewsCategory::create($request->validated());

        return redirect()->route('admin.news-categories.index')->with('status', 'Category created.');
    }

    public function update(SaveNewsCategoryRequest $request, NewsCategory $news_category): RedirectResponse
    {
        $news_category->update($request->validated());

        return redirect()->route('admin.news-categories.index')->with('status', 'Category saved.');
    }

    public function destroy(NewsCategory $news_category): RedirectResponse
    {
        if ($news_category->articles()->exists()) {
            return back()->withErrors(['delete' => 'Move or delete the articles in this category before deleting it. You can unpublish it instead.']);
        }
        try {
            $news_category->delete();
        } catch (QueryException $exception) {
            report($exception);

            return back()->withErrors(['delete' => 'Category could not be deleted. Check for associated articles and try again.']);
        }

        return redirect()->route('admin.news-categories.index')->with('status', 'Category deleted.');
    }
}
