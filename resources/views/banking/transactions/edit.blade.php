<x-layouts.admin>
    <x-slot name="title">
        {{ trans('general.title.edit', ['type' => trans_choice('general.' . Str::plural($type), 1)]) }}
    </x-slot>

    <x-slot name="content">
        <div class="relative mt-4 w-full min-w-0">
            @if (($recurring = $transaction->recurring) && ($next = $recurring->getNextRecurring()))
                <div class="media mb-3">
                    <div class="media-body">
                        <div class="media-comment-text">
                            <div class="d-flex">
                                <h5 class="mt-0">{{ trans('recurring.recurring') }}</h5>
                            </div>

                            <p class="text-sm lh-160 mb-0">
                                {{
                                    trans('recurring.message', [
                                        'type' => mb_strtolower(trans_choice('general.transactions', 1)),
                                        'date' => $next->format($date_format)
                                    ])
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <x-form id="transaction" method="PATCH" :route="['transactions.update', $transaction->id]" :model="$transaction">
                <x-form.section>
                    <x-slot name="head">
                        <x-form.section.head title="{{ trans('general.general') }}" description="{{ trans('transactions.form_description.general') }}" />
                    </x-slot>

                    <x-slot name="body">
                        <div class="grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-5 sm:col-span-6">
                            <x-form.group.date name="paid_at" label="{{ trans('general.date') }}" icon="calendar_today" value="{{ Date::parse($transaction->paid_at)->toDateString() }}" show-date-format="{{ company_date_format() }}" date-format="Y-m-d" autocomplete="off" form-group-class="sm:col-span-3" />

                            <x-form.group.payment-method form-group-class="sm:col-span-3" />

                            <x-form.group.account form-group-class="sm:col-span-3" />

                            <x-form.group.money name="amount" label="{{ trans('general.amount') }}" :value="$transaction->amount" autofocus="autofocus" :currency="$currency" dynamicCurrency="currency" input="onChangeTax(form.tax_ids)" form-group-class="sm:col-span-3" />

                            <x-form.group.textarea name="description" label="{{ trans('general.description') }}" not-required form-group-class="sm:col-span-6" />

                            <x-form.input.hidden name="currency_code" :value="$transaction->currency_code" />
                            <x-form.input.hidden name="currency_rate" />
                        </div>
                    </x-slot>
                </x-form.section>

                <x-form.section>
                    <x-slot name="head">
                        <x-form.section.head title="{{ trans('general.assign') }}" description="{{ trans('transactions.form_description.assign_' . $type) }}" />
                    </x-slot>

                    <x-slot name="body">
                        <div class="grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-5 sm:col-span-6">
                            <x-form.group.category :type="$type" form-group-class="sm:col-span-3" />

                            @includeWhen(module_is_enabled('outlets'), 'outlets::partials.transaction-outlet-field', ['selected' => $transaction->outlet_id ?? old('outlet_id', '')])

                            <x-form.group.contact :type="$contact_type" not-required form-group-class="sm:col-span-3" />

                            <x-form.group.tax name="tax_ids" multiple with-summary not-required :currency="$currency" change="onChangeTax" form-group-class="sm:col-span-6" />

                            @if ($transaction->document)
                                <x-form.group.text name="document" label="{{ trans_choice('general.' . Str::plural(config('type.transaction.' . $type . '.document_type')), 1) }}" not-required disabled value="{{ $transaction->document->document_number }}" form-group-class="sm:col-span-6" />

                                <x-form.input.hidden name="document_id" :value="$transaction->document->id" />
                            @endif
                        </div>
                    </x-slot>
                </x-form.section>

                <x-form.section>
                    <x-slot name="head">
                        <x-form.section.head title="{{ trans_choice('general.others', 1) }}" description="{{ trans('transactions.form_description.other') }}" />
                    </x-slot>

                    <x-slot name="body">
                        <div class="grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-5 sm:col-span-6">
                            <x-form.group.text name="number" label="{{ trans_choice('general.numbers', 1) }}" form-group-class="sm:col-span-3" />

                            <x-form.group.text name="reference" label="{{ trans('general.reference') }}" not-required form-group-class="sm:col-span-3" />

                            <x-form.group.attachment form-group-class="sm:col-span-6" />
                        </div>
                    </x-slot>
                </x-form.section>

                @can('update-banking-transactions')
                <x-form.section>
                    <x-slot name="foot">
                        <x-form.buttons cancel-route="transactions.index" />
                    </x-slot>
                </x-form.section>
                @endcan

                <x-form.input.hidden name="type" :value="$transaction->type" />
            </x-form>
        </div>
    </x-slot>

    @push('scripts_start')
        <script type="text/javascript">
            var transaction_taxes = {!! json_encode($taxes) !!};

            if (typeof aka_currency !== 'undefined') {
                aka_currency = {!! json_encode(! empty($transaction) ? $transaction->currency : config('money.currencies.' . company()->currency)) !!};
            } else {
                var aka_currency = {!! json_encode(! empty($transaction) ? $transaction->currency : config('money.currencies.' . company()->currency)) !!};
            }
        </script>
    @endpush
    <x-script folder="banking" file="transactions" />
</x-layouts.admin>
