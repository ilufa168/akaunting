<x-table.td class="w-2/12" hidden-mobile>
    @if ($item->outlet_id && $item->outlet)
        {{ $item->outlet->name }}
    @else
        <x-empty-data />
    @endif
</x-table.td>
