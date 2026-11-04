<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBiographyRequest;
use App\Models\Biography;
use App\Services\CareerContentWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class BiographyController extends Controller
{
    public function edit(): View
    {
        return view('admin.biography.edit', ['title' => 'Biography', 'biography' => Biography::find(1) ?? new Biography]);
    }

    public function update(UpdateBiographyRequest $request, CareerContentWriter $writer): RedirectResponse
    {
        $record = Biography::find(1) ?? new Biography;
        $record->id = 1;
        $data = $request->safe()->except(['profile_image', 'banner_image', 'remove_profile_image', 'remove_banner_image']);
        $data['additional_details'] = $data['additional_details'] ?? [];
        try {
            $cleaned = $writer->save($record, $data, $request, ['profile_image', 'banner_image']);
        } catch (Throwable $exception) {
            report($exception);

            return back()->withInput($request->except(['profile_image', 'banner_image']))->withErrors(['save' => 'Biography could not be saved. Existing content and images were kept. Please try again.']);
        }

        return redirect()->route('admin.biography.edit')->with('status', 'Biography saved.')
            ->with('warning', $cleaned ? null : 'Saved successfully, but an unused old image could not be removed.');
    }
}
