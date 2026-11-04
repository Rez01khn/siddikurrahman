<?php

namespace App\Http\Requests\Admin;

class UploadPhotosRequest extends SavePhotoRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        unset($rules['image']);
        $rules['sort_order'] = ['required', 'integer', 'between:0,9991'];

        return $rules + ['images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => array_replace(UpdateBiographyRequest::imageRules(), [0 => 'required'])];
    }
}
