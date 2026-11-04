<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTrainingContentRequest;
use App\Services\TrainingImageWriter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class TrainingContentController extends Controller
{
    private function definition(Request $request): array
    {
        $d = config('training.'.$request->route('type'));
        abort_unless(is_array($d), 404);

        return $d;
    }

    private function record(Request $request): Model
    {
        return $this->definition($request)['model']::findOrFail($request->route('record'));
    }

    public function index(Request $request): View
    {
        $d = $this->definition($request);
        $f = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'publication' => ['nullable', 'in:published,draft'], 'featured' => ['nullable', 'in:yes,no']]);
        $q = $d['model']::query();
        if (! empty($f['q'])) {
            $q->where(fn ($q) => $q->where('title', 'like', '%'.$f['q'].'%')->orWhere('category', 'like', '%'.$f['q'].'%'));
        }if (! empty($f['publication'])) {
            $q->where('is_published', $f['publication'] === 'published');
        }if (isset($f['featured'])) {
            $q->where('is_featured', $f['featured'] === 'yes');
        }

        return view('admin.training.index', ['title' => $d['label'], 'type' => $request->route('type'), 'records' => $q->orderBy('sort_order')->orderBy('id')->paginate(15)->withQueryString()]);
    }

    public function create(Request $request): View
    {
        $d = $this->definition($request);

        return $this->form($request, new $d['model'](['sort_order' => 0]));
    }

    public function edit(Request $request): View
    {
        return $this->form($request, $this->record($request));
    }

    private function form(Request $request, Model $record): View
    {
        return view('admin.training.form', ['title' => $this->definition($request)['label'], 'type' => $request->route('type'), 'record' => $record]);
    }

    public function store(SaveTrainingContentRequest $request, TrainingImageWriter $writer): RedirectResponse
    {
        $d = $this->definition($request);

        return $this->persist($request, new $d['model'], $writer);
    }

    public function update(SaveTrainingContentRequest $request, TrainingImageWriter $writer): RedirectResponse
    {
        return $this->persist($request, $this->record($request), $writer);
    }

    private function persist(SaveTrainingContentRequest $request, Model $record, TrainingImageWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->save($record, $request->safe()->except(['image', 'remove_image']), $request);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput($request->except('image'))->withErrors(['save' => 'Unable to save. Existing content and images were retained.']);
        }

        return redirect()->route('admin.'.$request->route('type').'.index')->with('status', 'Content saved.')->with('warning', $ok ? null : 'An unused image could not be removed.');
    }

    public function destroy(Request $request, TrainingImageWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->delete($this->record($request));
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['delete' => 'Unable to delete content.']);
        }

        return redirect()->route('admin.'.$request->route('type').'.index')->with('status', 'Content deleted.')->with('warning', $ok ? null : 'An unused image could not be removed.');
    }
}
