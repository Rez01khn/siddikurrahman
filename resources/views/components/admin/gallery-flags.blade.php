@props(['record'])
<div class="bg-white border border-forest/10 rounded-xl p-6 space-y-5"><x-admin.career-field name="sort_order" label="Display order" type="number" :value="$record->sort_order ?? 0" required />
@foreach(['is_featured'=>'Featured','is_published'=>'Published'] as $name=>$label)<input type="hidden" name="{{ $name }}" value="0"><label class="flex items-center gap-3 text-sm"><input type="checkbox" name="{{ $name }}" value="1" @checked(old($name,$record->$name))>{{ $label }}</label>@endforeach</div>
