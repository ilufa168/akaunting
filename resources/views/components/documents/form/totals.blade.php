<div class="sm:col-span-6 mt-6" role="group" aria-labelledby="totals-section-label">
    <div id="totals-section-label" class="sr-only">{{ trans('invoices.sub_total') }} — {{ trans('invoices.discount') }} — {{ trans_choice('general.taxes', 2) }} — {{ trans('invoices.total') }}</div>
    
    <div class="flex justify-end">
        <table id="totals" class="w-full max-w-md">
            <colgroup>
                <col style="width: 50%;">
                <col style="width: 40%;">
                <col style="width: 10%;">
            </colgroup>

            <tbody id="invoice-total-rows" class="space-y-2">
                @stack('sub_total_td_start')

                <tr id="tr-subtotal">
                    <td class="py-2 ltr:text-right rtl:text-left font-medium text-gray-700 align-middle">
                        {{ trans('invoices.sub_total') }}
                    </td>

                    <td class="py-2 ltr:text-right rtl:text-left align-middle">
                        <x-form.input.money
                            name="sub_total"
                            value="0"
                            disabled
                            row-input
                            v-model="totals.sub"
                            :currency="$currency"
                            dynamicCurrency="currency"
                            money-class="ltr:text-right rtl:text-left disabled-money"
                            form-group-class="ltr:text-right rtl:text-left disabled-money"
                        />
                    </td>

                    <td class="py-2"></td>
                </tr>

                @stack('sub_total_td_end')

                @if (in_array(setting('localisation.discount_location', 'total'), ['item', 'both']))
                    @stack('item_discount_td_start')

                    <tr id="tr-line-discount" v-if="totals.item_discount">
                        <td class="py-2 ltr:text-right rtl:text-left font-medium text-gray-700 align-middle">
                            {{ trans('invoices.item_discount') }}
                        </td>

                        <td class="py-2 ltr:text-right rtl:text-left align-middle">
                            <x-form.input.money
                                name="item_discount"
                                value="0"
                                disabled
                                row-input
                                v-model="totals.item_discount"
                                :currency="$currency"
                                dynamicCurrency="currency"
                                money-class="ltr:text-right rtl:text-left disabled-money"
                                form-group-class="ltr:text-right rtl:text-left disabled-money"
                            />
                        </td>

                        <td class="py-2"></td>
                    </tr>

                    @stack('item_discount_td_end')
                @endif

                @if (in_array(setting('localisation.discount_location', 'total'), ['total', 'both']))
                    @stack('add_discount_td_start')

                    <tr id="tr-discount">
                        <td class="py-2 ltr:text-right rtl:text-left align-middle">
                            <div v-if="show_discount_text" @click="onAddDiscount()">
                                <x-button.hover color="to-purple">
                                    {{ trans('invoices.add_discount') }}
                                </x-button.hover>
                            </div>

                            <span v-if="totals.discount_text" v-html="totals.discount_text"></span>

                            <div class="flex items-center justify-end gap-2" v-if="show_discount">
                                <div class="flex items-center bg-gray-200 p-1 rounded-lg">
                                    <button type="button"
                                        class="w-7 h-7 flex justify-center items-center rounded transition-colors"
                                        :class="[{'text-gray-500': form.discount_type !== 'percentage'}, {'bg-white text-purple shadow-sm': form.discount_type === 'percentage'}]"
                                        @click="onChangeDiscountType('percentage')"
                                    >
                                        <span class="material-icons text-base">percent</span>
                                    </button>

                                    <button type="button"
                                        class="w-7 h-7 flex justify-center items-center rounded transition-colors"
                                        :class="[{'text-gray-500': form.discount_type !== 'fixed'}, {'bg-white text-purple shadow-sm': form.discount_type === 'fixed'}]"
                                        @click="onChangeDiscountType('fixed')"
                                    >
                                        {{ $currency->symbol }}
                                    </button>
                                </div>

                                <x-form.group.text name="pre_discount" id="pre-discount" form-group-class="w-24" v-model="form.discount" @input="onAddTotalDiscount" />
                            </div>
                        </td>

                        <td class="py-2 ltr:text-right rtl:text-left align-middle relative">
                            <x-form.input.money
                                name="discount_total"
                                value="0"
                                disabled
                                row-input
                                v-model="totals.discount"
                                :currency="$currency"
                                dynamicCurrency="currency"
                                money-class="ltr:text-right rtl:text-left disabled-money"
                                form-group-class="ltr:text-right rtl:text-left disabled-money"
                            />

                            <x-form.input.hidden name="discount_type" value="{{ $document->discount_type ?? 'percentage' }}" v-model="form.discount_type" />
                            <x-form.input.hidden name="discount" value="{{ $document->discount_rate ?? 0 }}" v-model="form.discount" />

                            <button v-if="delete_discount" type="button" @click="onRemoveDiscountArea()" aria-label="{{ trans('general.delete') }} {{ trans('invoices.discount') }}" class="material-icons-outlined absolute -right-8 top-1/2 -translate-y-1/2 w-6 h-7 flex justify-center text-lg text-gray-400 hover:text-gray-600 cursor-pointer border-0 bg-transparent p-0 transition-colors">
                                delete
                            </button>
                        </td>

                        <td class="py-2"></td>
                    </tr>

                    @stack('add_discount_td_end')
                @endif

                @stack('tax_total_td_start')

                <tr v-for="(tax, tax_index) in totals.taxes" :index="tax_index">
                    <td class="py-2 ltr:text-right rtl:text-left font-medium text-gray-700 align-middle">
                        <span v-html="tax.name"></span>
                    </td>

                    <td class="py-2 ltr:text-right rtl:text-left align-middle">
                        <x-form.input.money
                            name="tax_total"
                            value="0"
                            disabled
                            row-input
                            v-model="tax.total"
                            :currency="$currency"
                            dynamicCurrency="currency"
                            money-class="ltr:text-right rtl:text-left disabled-money"
                            form-group-class="ltr:text-right rtl:text-left disabled-money"
                        />
                    </td>

                    <td class="py-2"></td>
                </tr>

                @stack('tax_total_td_end')

                @stack('grand_total_td_start')

                <tr id="tr-total" class="border-t border-gray-200">
                    <td class="py-4 ltr:text-right rtl:text-left align-middle">
                        <div class="flex items-center justify-end gap-3">
                            <span class="font-bold text-gray-900">
                                {{ trans('invoices.total') }}
                            </span>

                            <x-form.group.select
                                name="currency_code"
                                :options="$currencies"
                                selected="{{ $currency->code }}"
                                change="onChangeCurrency"
                                model="form.currency_code"
                                add-new
                                add-new-text="{!! trans('general.title.new', ['type' => trans_choice('general.currencies', 1)]) !!}"
                                :path="route('modals.currencies.create')"
                                :field="[
                                    'key' => 'code',
                                    'value' => 'name'
                                ]"
                                form-group-class="w-24"
                            />

                            <x-form.input.hidden name="currency_rate" :value="(!empty($document)) ? $document->currency_rate : $currency->rate" />
                        </div>
                    </td>

                    <td class="py-4 ltr:text-right rtl:text-left align-middle">
                        <x-form.input.money
                            name="grand_total"
                            value="0"
                            disabled
                            row-input
                            v-model="totals.total"
                            :currency="$currency"
                            dynamicCurrency="currency"
                            money-class="ltr:text-right rtl:text-left disabled-money font-bold text-lg"
                            form-group-class="ltr:text-right rtl:text-left disabled-money"
                        />
                    </td>

                    <td class="py-4"></td>
                </tr>

                @stack('grand_total_td_end')

                @stack('currency_conversion_td_start')

                <tr id="tr-currency-conversion" :class="[
                    {'hidden': ! (('{{ $currency->code }}' != form.currency_code) && totals.total || dropdown_visible)},
                    {'table-row': (('{{ $currency->code }}' != form.currency_code) && totals.total || dropdown_visible)}
                ]">
                    <td class="py-2"></td>

                    <td colspan="2" class="py-2 ltr:text-right rtl:text-left align-middle">
                        <akaunting-currency-conversion
                            currency-conversion-text="{{ trans('currencies.conversion') }}"
                            :price="(totals.total / form.currency_rate).toFixed(2)"
                            :currency-code="form.currency_code"
                            :currency-rate="form.currency_rate"
                            :currency-symbol="currency_symbol"
                            @change="form.currency_rate = $event"
                        ></akaunting-currency-conversion>
                    </td>
                </tr>

                @stack('currency_conversion_td_end')
            </tbody>
        </table>
    </div>
</div>
