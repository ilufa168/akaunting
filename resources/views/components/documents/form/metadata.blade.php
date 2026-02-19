<div class="grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-5 my-3.5">
    {{-- Row 1: Customer | Invoice Date | Invoice Number --}}
    <div class="sm:col-span-2">
        <x-form.label for="contact" required>
            {{ trans_choice($textContact, 1) }}
        </x-form.label>

        <x-documents.form.contact
            type="{{ $typeContact }}"
            :contact="$contact"
            :contacts="$contacts"
            :search-route="$searchContactRoute"
            :create-route="$createContactRoute"
            :required="true"
            error="form.errors.get('contact_name')"
            :text-add-contact="$textAddContact"
            :text-create-new-contact="$textCreateNewContact"
            :text-edit-contact="$textEditContact"
            :text-contact-info="$textContactInfo"
            :text-choose-different-contact="$textChooseDifferentContact"
        />
    </div>

    @stack('issue_start')

    @if (! $hideIssuedAt)
        @if ($type === 'bill')
            <div class="sm:col-span-2">
                <x-tooltip id="tooltip-issued-at" placement="bottom" message="{{ trans('bills.form_description.tooltip.bill_date') }}">
                    <x-form.group.date
                        name="issued_at"
                        label="{{ trans($textIssuedAt) }}"
                        icon="calendar_today"
                        value="{{ $issuedAt }}"
                        show-date-format="{{ company_date_format() }}"
                        date-format="Y-m-d"
                        autocomplete="off"
                        change="setDueMinDate"
                    />
                </x-tooltip>
            </div>
        @else
            <div class="sm:col-span-2">
                <x-form.group.date
                    name="issued_at"
                    label="{{ trans($textIssuedAt) }}"
                    icon="calendar_today"
                    value="{{ $issuedAt }}"
                    show-date-format="{{ company_date_format() }}"
                    date-format="Y-m-d"
                    autocomplete="off"
                    change="setDueMinDate"
                />
            </div>
        @endif
    @endif

    @stack('document_number_start')

    @if (! $hideDocumentNumber)
        <div class="sm:col-span-2">
            <x-form.group.text
                name="document_number"
                label="{{ trans($textDocumentNumber) }}"
                value="{{ $documentNumber }}"
            />
        </div>
    @endif

    {{-- Row 2: Due Date | Order Number --}}
    @stack('order_number_start')

    @includeWhen(module_is_enabled('outlets'), 'outlets::partials.outlet-field', ['document' => $document ?? null])

    @stack('due_start')

    @if (! $hideDueAt)
        @if ($type === 'bill')
            <div class="sm:col-span-2">
                <x-tooltip id="tooltip-due-at" placement="bottom" message="{{ trans('bills.form_description.tooltip.due_date') }}">
                    <x-form.group.date
                        name="due_at"
                        label="{{ trans($textDueAt) }}"
                        icon="calendar_today"
                        value="{{ $dueAt }}"
                        show-date-format="{{ company_date_format() }}"
                        date-format="Y-m-d"
                        autocomplete="off"
                        period="{{ $periodDueAt }}"
                        min-date="form.issued_at"
                        min-date-dynamic="min_due_date"
                        data-value-min
                    />
                </x-tooltip>
            </div>
        @else
            <div class="sm:col-span-2">
                <x-form.group.date
                    name="due_at"
                    label="{{ trans($textDueAt) }}"
                    icon="calendar_today"
                    value="{{ $dueAt }}"
                    show-date-format="{{ company_date_format() }}"
                    date-format="Y-m-d"
                    autocomplete="off"
                    period="{{ $periodDueAt }}"
                    min-date="form.issued_at"
                    min-date-dynamic="min_due_date"
                    data-value-min
                />
            </div>
        @endif
    @else
        <x-form.input.hidden
            name="due_at"
            :value="old('issued_at', $issuedAt)"
            v-model="form.issued_at"
        />
    @endif

    @if (! $hideOrderNumber)
        <div class="sm:col-span-2">
            <x-form.group.text
                name="order_number"
                label="{{ trans($textOrderNumber) }}"
                value="{{ $orderNumber }}"
                not-required
            />
        </div>
    @endif
</div>
