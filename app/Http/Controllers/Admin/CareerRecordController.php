<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCareerRecordRequest;
use App\Services\CareerContentWriter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class CareerRecordController extends Controller
{
    private function definition(Request $request): array
    {
        $definition = config('career.modules.'.$request->route('type'));
        abort_unless(is_array($definition), 404);

        return $definition;
    }

    private function record(Request $request): Model
    {
        return $this->definition($request)['model']::findOrFail($request->route('record'));
    }

    public function index(Request $request): View
    {
        $type = $request->route('type');
        $definition = $this->definition($request);
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:published,draft'],
            'category' => ['nullable', Rule::in(array_keys(config('career.categories')))],
        ]);
        $query = $definition['model']::query();
        if ($search = $filters['q'] ?? null) {
            $query->where(function ($query) use ($search, $type): void {
                $query->where($type === 'statistics' ? 'label' : 'title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
                if ($type === 'achievements') {
                    $query->orWhere('event_name', 'like', '%'.$search.'%');
                }
            });
        }
        if ($status = $filters['status'] ?? null) {
            $query->where('is_published', $status === 'published');
        }
        if ($type === 'achievements' && ! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        return view('admin.career.index', [
            'title' => $definition['label'], 'type' => $type,
            'records' => $query->orderBy('sort_order')->orderBy('id')->paginate(15)->withQueryString(),
        ]);
    }

    public function create(Request $request): View
    {
        $definition = $this->definition($request);
        $model = $definition['model'];
        $defaults = ['sort_order' => 0, 'is_published' => false];
        if ($request->route('type') === 'achievements') {
            $defaults['is_featured'] = false;
        }

        return view('admin.career.form', [
            'title' => 'Add '.$definition['label'], 'type' => $request->route('type'),
            'record' => new $model($defaults),
        ]);
    }

    public function edit(Request $request): View
    {
        return view('admin.career.form', [
            'title' => 'Edit '.$this->definition($request)['label'], 'type' => $request->route('type'), 'record' => $this->record($request),
        ]);
    }

    public function store(SaveCareerRecordRequest $request, CareerContentWriter $writer): RedirectResponse
    {
        $model = $this->definition($request)['model'];

        return $this->persist($request, new $model, $writer);
    }

    public function update(SaveCareerRecordRequest $request, CareerContentWriter $writer): RedirectResponse
    {
        return $this->persist($request, $this->record($request), $writer);
    }

    private function persist(SaveCareerRecordRequest $request, Model $record, CareerContentWriter $writer): RedirectResponse
    {
        $type = $request->route('type');
        try {
            $cleaned = $writer->save($record, $request->safe()->except(['image', 'remove_image']), $request, $type === 'statistics' ? [] : ['image']);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput($request->except('image'))->withErrors(['save' => 'The record could not be saved. Existing content and images were kept. Please try again.']);
        }

        return redirect()->route('admin.'.$type.'.index')->with('status', 'Career record saved.')
            ->with('warning', $cleaned ? null : 'Saved successfully, but an unused old image could not be removed.');
    }

    public function toggle(Request $request): RedirectResponse
    {
        $record = $this->record($request);
        $changed = DB::transaction(function () use ($record): bool {
            $current = $record->newQuery()->lockForUpdate()->findOrFail($record->getKey());
            if (! $current->is_published && trim((string) $current->source_note) === '') {
                return false;
            }
            $current->update(['is_published' => ! $current->is_published]);

            return true;
        });

        return $changed ? back()->with('status', 'Publication status updated.')
            : back()->withErrors(['source_note' => 'Add a source or verification note before publishing.']);
    }

    public function destroy(Request $request, CareerContentWriter $writer): RedirectResponse
    {
        try {
            $cleaned = $writer->delete($this->record($request), $request->route('type') === 'statistics' ? [] : ['image']);
        } catch (ModelNotFoundException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['delete' => 'The record could not be deleted. Please try again.']);
        }

        return redirect()->route('admin.'.$request->route('type').'.index')->with('status', 'Career record deleted.')
            ->with('warning', $cleaned ? null : 'The record was deleted, but an unused image could not be removed.');
    }
}
