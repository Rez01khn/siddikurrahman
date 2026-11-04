<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function __invoke(string $module): View|RedirectResponse
    {
        if (in_array($module, ['legacy-imports', 'homepage', 'biography', 'milestones', 'achievements', 'statistics', 'tournaments', 'tournament-results', 'news', 'news-categories', 'photos', 'photo-albums', 'videos', 'sponsors', 'social-works', 'equipment', 'coaching', 'messages', 'settings', 'users'], true)) {
            return redirect()->route('admin.'.$module.'.'.(in_array($module, ['biography', 'settings'], true) ? 'edit' : 'index'));
        }
        $modules = config('admin.modules');
        abort_unless(array_key_exists($module, $modules), 404);

        return view('admin.module', ['title' => $modules[$module]]);
    }
}
