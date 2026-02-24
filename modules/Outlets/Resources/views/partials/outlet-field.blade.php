@php
    $outlets = \Modules\Outlets\Models\Outlet::enabled()->orderBy('name')->pluck('name', 'id')->toArray();
    $outlets = ['' => trans('outlets::general.unallocated')] + $outlets;
    $selected = old('outlet_id', optional($document ?? null)->outlet_id ?: setting('default.outlet', ''));
    $required = setting('outlets.required', false);
@endphp
<div class="sm:col-span-2 w-full">
    <x-form.group.select
        name="outlet_id"
        label="{{ trans_choice('outlets::general.outlets', 1) }}"
        :options="$outlets"
        :selected="$selected"
        :not-required="!$required"
        form-group-class="sm:col-span-2 w-full"
    />
</div>
