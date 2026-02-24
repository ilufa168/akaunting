<x-layouts.admin>
    <x-slot name="title">
        {{ trans_choice('outlets::general.outlets', 2) }}
    </x-slot>

    <x-slot name="favorite"
        title="{{ trans_choice('outlets::general.outlets', 2) }}"
        icon="store"
        route="outlets.index"
    ></x-slot>

    <x-slot name="buttons">
        @can('create-outlets-main')
            <x-link href="{{ route('outlets.create') }}" kind="primary" id="index-more-actions-new-outlet">
                {{ trans('general.title.new', ['type' => trans_choice('outlets::general.outlets', 1)]) }}
            </x-link>
        @endcan
    </x-slot>

    <x-slot name="content">
        <x-index.container>
            <x-index.search
                search-string="Modules\Outlets\Models\Outlet"
            />

            <x-table>
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th class="w-6/12 sm:w-5/12">
                            <x-slot name="first">
                                <x-sortablelink column="name" title="{{ trans('general.name') }}" />
                            </x-slot>
                            <x-slot name="second">
                                {{ trans('general.address') }}
                            </x-slot>
                        </x-table.th>

                        <x-table.th class="w-4/12" hidden-mobile>
                            <x-slot name="first">
                                {{ trans('general.phone') }}
                            </x-slot>
                            <x-slot name="second">
                                {{ trans('general.email') }}
                            </x-slot>
                        </x-table.th>

                        <x-table.th class="w-2/12">
                            <x-sortablelink column="enabled" title="{{ trans('general.enabled') }}" />
                        </x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <x-table.tbody>
                    @foreach($outlets as $item)
                        <x-table.tr href="{{ route('outlets.edit', $item->id) }}">
                            <x-table.td class="w-6/12 sm:w-5/12">
                                <x-slot name="first" class="flex font-bold">
                                    {{ $item->name }}

                                    @if (! $item->enabled)
                                        <x-index.disable text="{{ trans_choice('outlets::general.outlets', 1) }}" />
                                    @endif
                                </x-slot>
                                <x-slot name="second" class="font-normal truncate">
                                    @if (! empty($item->address))
                                        {{ \Illuminate\Support\Str::limit($item->address, 50) }}
                                    @else
                                        <x-empty-data />
                                    @endif
                                </x-slot>
                            </x-table.td>

                            <x-table.td class="w-4/12" hidden-mobile>
                                <x-slot name="first">
                                    {{ $item->phone ?? '-' }}
                                </x-slot>
                                <x-slot name="second">
                                    {{ $item->email ?? '-' }}
                                </x-slot>
                            </x-table.td>

                            <x-table.td class="w-2/12">
                                @if ($item->enabled)
                                    <x-show.status :text-status="trans('general.yes')" background-color="bg-success" text-color="text-text-success" />
                                @else
                                    <x-show.status :text-status="trans('general.no')" background-color="bg-danger" text-color="text-text-danger" />
                                @endif
                            </x-table.td>

                            <x-table.td kind="action">
                                <x-table.actions :model="$item" />
                            </x-table.td>
                        </x-table.tr>
                    @endforeach
                </x-table.tbody>
            </x-table>

            <x-pagination :items="$outlets" />
        </x-index.container>
    </x-slot>
</x-layouts.admin>
