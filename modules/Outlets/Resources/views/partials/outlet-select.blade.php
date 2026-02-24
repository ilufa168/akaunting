@php
    $outlets = \Modules\Outlets\Models\Outlet::enabled()->orderBy('name')->pluck('name', 'id')->toArray();
    $outlets = ['' => trans('outlets::general.unallocated')] + $outlets;
@endphp
<x-form.group.select
    name="outlet_id"
    label="{{ trans_choice('outlets::general.outlets', 1) }}"
    :options="$outlets"
    not-required
    {{ $attributes ?? '' }}
/>
