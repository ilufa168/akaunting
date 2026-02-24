<x-layouts.admin>
    <x-slot name="title">
        {{ $outlet->name }}
    </x-slot>

    <x-slot name="favorite"
        title="{{ $outlet->name }}"
        icon="store"
        url="{{ route('outlets.show', $outlet->id) }}"
    ></x-slot>

    <x-slot name="buttons">
        @can('update-outlets-main')
            <x-link href="{{ route('outlets.edit', $outlet->id) }}" kind="primary">
                {{ trans('general.edit') }}
            </x-link>
        @endcan
    </x-slot>

    <x-slot name="content">
        <x-show.container>
            <x-show.section>
                <x-slot name="head">
                    <x-show.section.head title="{{ trans('general.general') }}" />
                </x-slot>

                <x-slot name="body">
                    <x-show.group.text name="name" value="{{ $outlet->name }}" title="{{ trans('general.name') }}" />
                    <x-show.group.text name="address" value="{{ $outlet->address }}" title="{{ trans('general.address') }}" />
                    <x-show.group.text name="phone" value="{{ $outlet->phone }}" title="{{ trans('general.phone') }}" />
                    <x-show.group.text name="email" value="{{ $outlet->email }}" title="{{ trans('general.email') }}" />
                    <x-show.group.boolean name="enabled" value="{{ $outlet->enabled }}" title="{{ trans('general.enabled') }}" />
                </x-slot>
            </x-show.section>
        </x-show.container>
    </x-slot>
</x-layouts.admin>
