<x-form.accordion type="advanced">
    <x-slot name="head">
        <x-form.accordion.head
            title="{{ trans_choice($textSectionAdvancedTitle, 1) }}"
            description="{{ trans($textSectionAdvancedDescription, ['type' => $type]) }}"
        />
    </x-slot>

    <x-slot name="body">
        @stack('footer_start')

        <div class="grid grid-cols-1 sm:grid-cols-6 gap-x-6 gap-y-5 sm:col-span-6">
            @if (! $hideFooter)
                <x-form.group.textarea name="footer" label="{{ trans('general.footer') }}" class="h-full" :value="$footer" not-required rows="7" form-group-class="sm:col-span-6" />
            @endif

            @stack('category_start')

            @if (! $hideCategory)
                <x-form.group.category :type="$typeCategory" :selected="$categoryId" form-group-class="sm:col-span-3" />
            @else
                <x-form.input.hidden name="category_id" :value="$categoryId" />
            @endif

            @stack('attachment_end')

            @if (! $hideAttachment)
                <x-form.group.attachment form-group-class="sm:col-span-3" />
            @endif

            @if (! $hideTemplate)
                <x-form.group.select
                    name="template"
                    label="{{ trans_choice('general.templates', 1) }}"
                    :options="$templates"
                    :selected="$template"
                    option-style="height: 6rem;"
                    form-group-class="sm:col-span-3"
                >
                    <template #option="{option}">
                        <span class="w-full flex h-16 items-center gap-3">
                            <img :src="option.option.image" class="h-16 w-auto object-contain" :alt="option.option.name" />
                            
                            <div class="flex flex-col text-gray-900 text-sm font-medium">
                                <span>@{{ option.option.name }}</span>
                            </div>
                        </span>
                    </template>
                </x-form.group.select>
            @endif

            @if (! $hideBackgroundColor)
                <x-form.group.color name="color" label="{{ trans('general.color') }}" :value="$backgroundColor" form-group-class="sm:col-span-3" />
            @endif
        </div>
    </x-slot>
</x-form.accordion>
