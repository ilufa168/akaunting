<x-layouts.admin>
    <x-slot name="title">
        {{ trans('general.title.edit', ['type' => $outlet->name]) }}
    </x-slot>

    <x-slot name="favorite"
        title="{{ trans('general.title.edit', ['type' => $outlet->name]) }}"
        icon="store"
        url="{{ route('outlets.edit', $outlet->id) }}"
    ></x-slot>

    <x-slot name="content">
        <x-form.container>
            <x-form id="outlet" :route="['outlets.update', $outlet->id]" :model="$outlet">
                <x-form.section>
                    <x-slot name="head">
                        <x-form.section.head title="{{ trans('general.general') }}" description="{{ trans('outlets::general.description') }}" />
                    </x-slot>

                    <x-slot name="body">
                        <x-form.group.text name="name" label="{{ trans('general.name') }}" form-group-class="sm:col-span-6" />

                        <x-form.group.textarea name="address" label="{{ trans('general.address') }}" not-required />

                        <x-form.group.text name="phone" label="{{ trans('general.phone') }}" not-required />

                        <x-form.group.text name="email" label="{{ trans('general.email') }}" not-required />

                        <x-form.group.toggle name="enabled" label="{{ trans('general.enabled') }}" :value="$outlet->enabled" />
                    </x-slot>
                </x-form.section>

                <x-form.section>
                    <x-slot name="foot">
                        <x-form.buttons cancel-route="outlets.index" />
                    </x-slot>
                </x-form.section>
            </x-form>
        </x-form.container>
    </x-slot>
</x-layouts.admin>
