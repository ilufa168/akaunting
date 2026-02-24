@php
    $outlets = \Modules\Outlets\Models\Outlet::enabled()->orderBy('name')->pluck('name', 'id')->toArray();
    $outlets = ['' => trans('outlets::general.unallocated')] + $outlets;
    $selected = old('outlet_id', $selected ?: setting('default.outlet', ''));
    $required = setting('outlets.required', false);
@endphp
<x-form.group.select
    name="outlet_id"
    label="{{ trans_choice('outlets::general.outlets', 1) }}"
    :options="$outlets"
    :selected="$selected"
    :not-required="!$required"
/>
