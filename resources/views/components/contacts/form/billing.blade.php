<x-form.section>
    <x-slot name="head">
        <x-form.section.head
            title="{{ trans($textSectionBillingTitle) }}"
            description="{{ trans($textSectionBillingDescription) }}"
        />
    </x-slot>

    <x-slot name="body">
        <div class="grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-5 sm:col-span-6">
            @if (! $hideTaxNumber)
                <x-form.group.text name="tax_number" label="{{ trans($textTaxNumber) }}" not-required form-group-class="sm:col-span-3" />
            @endif

            @if (! $hideCurrency)
                <x-form.group.currency form-group-class="sm:col-span-3" />
            @endif
        </div>
    </x-slot>
</x-form.section>
