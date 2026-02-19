<div class="relative sm:col-span-6 w-full overflow-x-auto" role="region" aria-label="{{ trans_choice('general.items', 2) }}">
    <table class="w-full min-w-[600px]" id="items">
        <colgroup>
            <col style="width: 24px;">
            <col style="width: 20%;">
            <col style="width: 30%;">
            <col style="width: 12%;">
            <col style="width: 15%;">
            <col style="width: 20%;">
            <col style="width: 24px;">
        </colgroup>

        <thead class="border-b border-gray-200">
            <tr>
                @stack('move_th_start')

                <th class="w-6 p-2 text-left border-0" style="vertical-align:bottom;">
                    @if (! $hideEditItemColumns)
                        <x-documents.form.item-columns :type="$type" />
                    @endif
                </th>

                @stack('move_th_end')

                @if (! $hideItems)
                    @stack('name_th_start')

                    <th class="px-3 py-2 ltr:pl-2 rtl:pr-2 ltr:text-left rtl:text-right text-xs font-medium text-gray-700 border-0" style="vertical-align:bottom;">
                        @if (! $hideItemName)
                            {{ (trans_choice($textItemName, 2) != $textItemName) ? trans_choice($textItemName, 2) : trans($textItemName) }}

                            @if ($hideSettingItemName)
                                <x-tooltip id="tooltip-item-price" placement="top" message="{{ trans('documents.item_price_hidden', ['type' => config('type.document.' . $type . '.translation.prefix')]) }}">
                                    <x-icon icon="visibility_off" class="text-sm font-normal ml-1"></x-icon>
                                </x-tooltip>
                            @endif
                        @endif
                    </th>

                    @stack('name_th_end')

                    @stack('description_th_start')

                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 border-0" style="vertical-align:bottom;">
                        @if (! $hideItemDescription)
                            {{ trans($textItemDescription) }}

                            @if ($hideSettingItemDescription)
                                <x-tooltip id="tooltip-item-price" placement="top" message="{{ trans('documents.item_price_hidden', ['type' => config('type.document.' . $type . '.translation.prefix')]) }}">
                                    <x-icon icon="visibility_off" class="text-sm font-normal ml-1"></x-icon>
                                </x-tooltip>
                            @endif
                        @endif
                    </th>

                    @stack('description_th_end')
                @endif

                @stack('quantity_th_start')

                <th class="px-3 py-2 ltr:text-left rtl:text-right text-xs font-medium text-gray-700 border-0" style="vertical-align:bottom;">
                    @if (! $hideItemQuantity)
                        {{ trans($textItemQuantity) }}

                        @if ($hideSettingItemQuantity)
                            <x-tooltip id="tooltip-item-price" placement="top" message="{{ trans('documents.item_price_hidden', ['type' => config('type.document.' . $type . '.translation.prefix')]) }}">
                                <x-icon icon="visibility_off" class="text-sm font-normal ml-1"></x-icon>
                            </x-tooltip>
                        @endif
                    @endif
                </th>

                @stack('quantity_th_end')

                @stack('price_th_start')

                <th class="px-3 py-2 ltr:text-left rtl:text-right text-xs font-medium text-gray-700 border-0" style="vertical-align:bottom;">
                    @if (! $hideItemPrice)
                        {{ trans($textItemPrice) }}

                        @if ($hideSettingItemPrice)
                            <x-tooltip id="tooltip-item-price" placement="top" message="{{ trans('documents.item_price_hidden', ['type' => config('type.document.' . $type . '.translation.prefix')]) }}">
                                <x-icon icon="visibility_off" class="text-sm font-normal ml-1"></x-icon>
                            </x-tooltip>
                        @endif
                    @endif
                </th>

                @stack('price_th_end')

                @stack('total_th_start')

                <th class="px-3 py-2 ltr:text-right rtl:text-left text-xs font-medium text-gray-700 border-0" style="vertical-align:bottom;">
                    @if (! $hideItemAmount)
                        {{ trans($textItemAmount) }}

                        @if ($hideSettingItemAmount)
                            <x-tooltip id="tooltip-item-price" placement="top" message="{{ trans('documents.item_price_hidden', ['type' => config('type.document.' . $type . '.translation.prefix')]) }}">
                                <x-icon icon="visibility_off" class="text-sm font-normal ml-1"></x-icon>
                            </x-tooltip>
                        @endif
                    @endif
                </th>

                @stack('total_th_end')

                @stack('remove_th_start')

                <th class="p-2 border-0" style="width:24px; vertical-align:bottom;">
                </th>

                @stack('remove_th_end')
            </tr>
        </thead>

        <tbody id="{{ (! $hideDiscount && in_array(setting('localisation.discount_location', 'total'), ['item', 'both'])) ? 'invoice-item-discount-rows' : 'invoice-item-rows' }}" class="divide-y divide-gray-100">
            <x-documents.form.line-item :type="$type" />

            @stack('add_item_td_start')

            <tr id="addItem">
                <td colspan="7" class="p-0">
                    <x-documents.form.item-button
                        type="{{ $type }}"
                        is-sale="{{ $isSalePrice }}"
                        is-purchase="{{ $isPurchasePrice }}"
                        search-char-limit="{{ $searchCharLimit }}"
                    />
                </td>
            </tr>

            @stack('add_item_td_end')
        </tbody>
    </table>
</div>
