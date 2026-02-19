<div class="sm:col-span-6 my-6">
    <x-form.group.textarea
        name="notes"
        label="{{ trans_choice('general.notes', 2) }}"
        :value="$notes"
        not-required
        form-group-class="w-full"
        rows="2"
        textarea-auto-height
    />
</div>
