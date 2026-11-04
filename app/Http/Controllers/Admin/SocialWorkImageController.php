<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveSocialWorkImageRequest;
use App\Models\SocialWork;
use App\Models\SocialWorkImage;
use App\Services\CommunityWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class SocialWorkImageController extends Controller
{
    public function edit(SocialWork $social_work, SocialWorkImage $image): View
    {
        return view('admin.community.image-form', ['title' => 'Project Image', 'project' => $social_work, 'image' => $image]);
    }

    public function update(SaveSocialWorkImageRequest $request, SocialWork $social_work, SocialWorkImage $image, CommunityWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->save($image, $request->safe()->except('image'), $request, 'image');
        } catch (Throwable $e) {
            report($e);

            return back()->withInput($request->except('image'))->withErrors(['save' => 'Image could not be saved. Existing content and files were kept.']);
        }

        return redirect()->route('admin.social-works.edit', $social_work)->with('status', 'Project image saved.')->with('warning', $ok ? null : 'An unused image could not be removed.');
    }

    public function destroy(SocialWork $social_work, SocialWorkImage $image, CommunityWriter $writer): RedirectResponse
    {
        try {
            $ok = $writer->delete($image);
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['delete' => 'Image could not be deleted.']);
        }

        return redirect()->route('admin.social-works.edit', $social_work)->with('status', 'Project image deleted.')->with('warning',$ok ? null : 'An unused image could not be removed.');
    }
}
