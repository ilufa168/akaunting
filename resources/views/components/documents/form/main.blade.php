<x-form.section override="class" class="mb-14">
    <x-slot name="head">
        <x-form.section.head title="{{ trans($textSectionMainTitle) }}" description="{{ trans($textSectionMainDescription) }}" />
    </x-slot>

    <x-slot name="body" override="class">
        <div class="my-3.5 sm:col-span-6">
            <x-documents.form.metadata type="{{ $type }}" :document="$document" />

            <x-documents.form.items type="{{ $type }}" />

            <x-documents.form.totals type="{{ $type }}" />

            <x-documents.form.note type="{{ $type }}" />
        </div>
    </x-slot>
</x-form.section>