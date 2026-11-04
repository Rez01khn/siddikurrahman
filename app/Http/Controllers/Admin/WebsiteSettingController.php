<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWebsiteSettingRequest;
use App\Models\WebsiteSetting;
use App\Services\CareerContentWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class WebsiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings', ['title' => 'Website Settings', 'settings' => WebsiteSetting::find(1) ?? new WebsiteSetting]);
    }

    public function update(UpdateWebsiteSettingRequest $request, CareerContentWriter $writer): RedirectResponse
    {
        $settings = WebsiteSetting::firstOrCreate(['id' => 1]);
        try {
            $ok = $writer->save($settings, $request->validated(), $request, ['logo', 'favicon'], 'settings');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput($request->except(['logo', 'favicon']))->withErrors(['save' => 'Unable to save website settings.']);
        }

        return redirect()->route('admin.settings.edit')->with('status', 'Website settings saved.')->with('warning', $ok ? null : 'An unused image could not be removed.');
    }
}
