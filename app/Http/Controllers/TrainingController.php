<?php

namespace App\Http\Controllers;

use App\Models\CoachingTip;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainingController extends Controller
{
    private function layout(): array
    {
        return ['sections' => collect(), 'visibleAnchors' => ['top'], 'solidHeader' => true];
    }

    public function equipment(Request $request): View
    {
        return $this->listing($request, Equipment::class, 'In The Bag', 'equipment');
    }

    public function tips(Request $request): View
    {
        return $this->listing($request, CoachingTip::class, 'Coaching & Tips', 'coaching');
    }

    private function listing(Request $request, string $model, string $title, string $type): View
    {
        $f = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'category' => ['nullable', 'string', 'max:100']]);
        $q = $model::where('is_published', true);
        if (! empty($f['q'])) {
            $q->where('title', 'like', '%'.$f['q'].'%');
        }if (! empty($f['category'])) {
            $q->where('category', $f['category']);
        }

        return view('training.index', $this->layout() + ['title' => $title, 'type' => $type, 'records' => $q->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id')->paginate(12)->withQueryString(), 'categories' => $model::where('is_published', true)->whereNotNull('category')->distinct()->orderBy('category')->pluck('category')]);
    }

    public function tip(CoachingTip $tip): View
    {
        abort_unless($tip->is_published, 404);

        return view('training.tip', $this->layout() + ['tip' => $tip]);
    }
}
