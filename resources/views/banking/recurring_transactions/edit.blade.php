<x-layouts.admin>
    <x-slot name="title">
        {{ trans('general.title.edit', ['type' => trans_choice('general.recurring_' . Str::plural($real_type), 1)]) }}
    </x-slot>

    <x-slot name="content">
        <div class="relative mt-4 w-full min-w-0">
            <x-form id="transaction" method="PATCH" :route="['recurring-transactions.update', $recurring_transaction->id]" :model="$recurring_transaction">
                <x-form.section>
                    <x-slot name="head">
                        <x-form.section.head title="{{ trans('general.general') }}" description="{{ trans('transactions.form_description.general') }}" />
                    </x-slot>

                    <x-slot name="body">
                        <div class="grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-5 sm:col-span-6">
                            <div class="relative sm:col-span-3">
                            <x-form.label>
                                {{ trans('general.date') }}
                            </x-form.label>

                            <x-tooltip id="tooltip-paid" placement="bottom" message="{{ trans('documents.recurring.tooltip.document_date', ['type' => Str::lower(trans_choice('general.transactions', 1))]) }}">
                                <div class="relative focused has-label">
                                    <x-form.input.text name="disabled_transaction_paid" value="{{ trans('documents.recurring.auto_generated') }}" disabled />
                                </div>
                            </x-tooltip>

                            <x-form.input.hidden name="paid_at" :value="$recurring_transaction->paid_at" />
                            </div>

                            <x-form.group.money name="amount" label="{{ trans('general.amount') }}" :value="$recurring_transaction->amount" autofocus="autofocus" :currency="$currency" dynamicCurrency="currency" form-group-class="sm:col-span-3" />

                            <x-form.group.account form-group-class="sm:col-span-3" />

                            <x-form.group.payment-method form-group-class="sm:col-span-3" />

                            <x-form.group.textarea name="description" label="{{ trans('general.description') }}" not-required form-group-class="sm:col-span-6" />

                            <x-form.input.hidden name="currency_code" :value="$recurring_transaction->currency_code" />
                            <x-form.input.hidden name="currency_rate" />
                        </div>
                    </x-slot>
                </x-form.section>

                <x-form.section>
                    <x-slot name="head">
                        <x-form.section.head title="{{ trans('general.assign') }}" description="{{ trans('transactions.form_description.assign_' . $real_type) }}" />
                    </x-slot>

                    <x-slot name="body">
                        <div class="grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-5 sm:col-span-6">
                            <x-form.group.category type="{{ $real_type }}" form-group-class="sm:col-span-3" />

                            @includeWhen(module_is_enabled('outlets'), 'outlets::partials.transaction-outlet-field', ['selected' => $recurring_transaction->outlet_id ?? old('outlet_id', '')])

                            <x-form.group.contact :type="$contact_type" not-required form-group-class="sm:col-span-3" />

                            <x-form.group.tax name="tax_ids" multiple with-summary not-required :currency="$currency" change="onChangeTax" form-group-class="sm:col-span-6" />
                        </div>
                    </x-slot>
                </x-form.section>

                <x-form.section>
                    <x-slot name="head">
                        <x-form.section.head title="{{ trans('recurring.recurring') }}" description="{{ trans('recurring.form_description.schedule', ['type' => Str::lower(trans_choice('general.transactions', 1))]) }}" />
                    </x-slot>

                    <x-slot name="body">
                        <div class="sm:col-span-6">
                            <x-form.group.recurring
                            type="transaction"
                            :interval="$recurring_transaction ? $recurring_transaction->recurring->interval : null"
                            :frequency="$recurring_transaction ? $recurring_transaction->recurring->frequency : null"
                            :custom-frequency="$recurring_transaction ? $recurring_transaction->recurring->custom_frequency : null"
                            :limit="$recurring_transaction ? $recurring_transaction->recurring->limit_by : null"
                            :started-value="$recurring_transaction ? $recurring_transaction->recurring->started_at : null"
                            :limit-count="$recurring_transaction ? $recurring_transaction->recurring->limit_count : null"
                            :limit-date-value="$recurring_transaction ? $recurring_transaction->recurring->limit_date : null"
                            :send-email="$recurring_transaction ? $recurring_transaction->recurring->auto_send : null"
                        />
                        </div>
                    </x-slot>
                </x-form.section>

                <x-form.section>
                    <x-slot name="head">
                        <x-form.section.head title="{{ trans_choice('general.others', 1) }}" description="{{ trans('transactions.form_description.other') }}" />
                    </x-slot>

                    <x-slot name="body">
                        <div class="grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-5 sm:col-span-6">
                            <div class="relative sm:col-span-3">
                                <x-form.label>
                                    {{ trans_choice('general.numbers', 1) }}
                                </x-form.label>

                                <x-tooltip id="tooltip-number" placement="bottom" message="{{ trans('documents.recurring.tooltip.document_number', ['type' => Str::lower(trans_choice('general.transactions', 1))]) }}">
                                    <div class="relative focused has-label">
                                        <x-form.input.text name="disabled_transaction_number" value="{{ trans('documents.recurring.auto_generated') }}" disabled />
                                    </div>
                                </x-tooltip>

                                <x-form.input.hidden name="number" value="{{ $number }}" />
                            </div>

                            <x-form.group.text name="reference" label="{{ trans('general.reference') }}" not-required form-group-class="sm:col-span-3" />

                            <x-form.group.attachment form-group-class="sm:col-span-6" />
                        </div>
                    </x-slot>
                </x-form.section>

                @can('update-banking-transactions')
                <x-form.section>
                    <x-slot name="foot">
                        <x-form.buttons cancel-route="recurring-transactions.index" />
                    </x-slot>
                </x-form.section>
                @endcan

                <x-form.input.hidden name="type" :value="$recurring_transaction->type" />
                <x-form.input.hidden name="real_type" :value="$real_type" />
            </x-form>
        </div>
    </x-slot>

    @push('scripts_start')
        <script type="text/javascript">
            var transaction_taxes = {!! json_encode($taxes) !!};
            
            if (typeof aka_currency !== 'undefined') {
                aka_currency = {!! json_encode(! empty($recurring_transaction) ? $recurring_transaction->currency : config('money.currencies.' . company()->currency)) !!};
            } else {
                var aka_currency = {!! json_encode(! empty($recurring_transaction) ? $recurring_transaction->currency : config('money.currencies.' . company()->currency)) !!};
            }
        </script>
    @endpush

    <x-script folder="banking" file="transactions" />
</x-layouts.admin>
