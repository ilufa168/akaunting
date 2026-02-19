<x-form.section>
    <x-slot name="head">
        <x-form.section.head
            title="{{ trans($textSectionAddressTitle) }}"
            description="{{ trans($textSectionAddressDescription) }}"
        />
    </x-slot>

    <x-slot name="body">
        @if (! $hideAddress)
            <x-form.group.textarea name="address" label="{{ trans($textAddress) }}" not-required v-model="form.address" form-group-class="sm:col-span-6" />
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-5 sm:col-span-6">
            @if (! $hideCity)
                <x-form.group.text name="city" label="{{ trans_choice($textCity, 1) }}" not-required form-group-class="sm:col-span-2" />
            @endif

            @if (! $hideState)
                <x-form.group.text name="state" label="{{ trans($textState) }}" not-required form-group-class="sm:col-span-2" />
            @endif

            @if (! $hideZipCode)
                <x-form.group.text name="zip_code" label="{{ trans($textZipCode) }}" not-required form-group-class="sm:col-span-2" />
            @endif

            @if (! $hideCountry)
                <x-form.group.country form-group-class="sm:col-span-3" not-required />
            @endif
        </div>
    </x-slot>
</x-form.section>
