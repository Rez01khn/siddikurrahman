@props(['name', 'label', 'value' => '', 'type' => 'text', 'required' => false, 'maxlength' => null])
@php
    $fieldId = str_replace('.', '-', $name);
    $parts = explode('.', $name);
    $inputName = array_shift($parts).implode('', array_map(fn ($part) => '['.$part.']', $parts));
@endphp
<div><label for="{{ $fieldId }}" class="block text-sm font-medium mb-2">{{ $label }} @if($required)<span aria-hidden="true" class="text-red-700">*</span>@endif</label>
@if($type === 'textarea')
<textarea id="{{ $fieldId }}" name="{{ $inputName }}" rows="5" @if($maxlength) maxlength="{{ $maxlength }}" @endif @required($required) @if($errors->has($name)) aria-invalid="true" aria-describedby="{{ $fieldId }}-error" @endif class="w-full border border-forest/25 rounded-lg p-3 text-base">{{ old($name, $value) }}</textarea>
@else
<input id="{{ $fieldId }}" name="{{ $inputName }}" type="{{ $type }}" value="{{ old($name, $value) }}" @if($maxlength) maxlength="{{ $maxlength }}" @endif @required($required) @if($errors->has($name)) aria-invalid="true" aria-describedby="{{ $fieldId }}-error" @endif class="w-full border border-forest/25 rounded-lg p-3 text-base">
@endif
@error($name)<p id="{{ $fieldId }}-error" class="text-sm text-red-700 mt-2">{{ $message }}</p>@enderror
</div>
