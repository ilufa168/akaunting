<x-form.section>
    <x-slot name="head">
        <x-form.section.head
            title="{{ trans_choice($textSectionPersonsTitle, 2) }}"
            description="{{ trans($textSectionPersonsDescription) }}"
        />
    </x-slot>

    <x-slot name="body">
        <div class="overflow-x-auto -mx-4 px-4 sm:mx-0 sm:px-0 sm:col-span-6">
            <x-table class="w-full min-w-[600px]">
                <x-table.thead>
                    <x-table.tr>
                        <x-table.th class="w-4/12 text-left py-2 px-3 text-xs font-medium text-gray-700">
                            {{ trans('general.name') }}
                        </x-table.th>

                        <x-table.th class="w-4/12 text-left py-2 px-3 text-xs font-medium text-gray-700">
                            {{ trans('general.email') }}
                        </x-table.th>

                        <x-table.th class="w-4/12 text-left py-2 px-3 text-xs font-medium text-gray-700">
                            {{ trans('general.phone') }}
                        </x-table.th>

                        <x-table.th class="w-10 text-right py-2 px-2"></x-table.th>
                    </x-table.tr>
                </x-table.thead>

                <x-table.tbody class="divide-y divide-gray-100">
                    <x-table.tr v-for="(row, index) in form.contact_persons" ::index="index" class="group hover:bg-gray-50 transition-colors">
                        <x-table.td class="w-4/12 py-2 px-3">
                            <x-form.group.text 
                                name="contact_persons[][name]" 
                                data-item="name" 
                                v-model="row.name" 
                                @change="forceUpdate()" 
                                placeholder="{{ trans('general.name') }}"
                                v-error="form.errors.has('contact_persons.' + index + '.name')"
                                v-error-message="form.errors.get('contact_persons.' + index + '.name')"
                                form-group-class="mb-0"
                            />
                        </x-table.td>

                        <x-table.td class="w-4/12 py-2 px-3">
                            <x-form.group.text 
                                name="contact_persons[][email]" 
                                data-item="email" 
                                v-model="row.email" 
                                @change="forceUpdate()" 
                                placeholder="{{ trans('general.email') }}" 
                                v-error="form.errors.has('contact_persons.' + index + '.email')" 
                                v-error-message="form.errors.get('contact_persons.' + index + '.email')"
                                form-group-class="mb-0"
                            />
                        </x-table.td>

                        <x-table.td class="w-4/12 py-2 px-3">
                            <x-form.group.text 
                                name="contact_persons[][phone]" 
                                data-item="phone" 
                                v-model="row.phone" 
                                @change="forceUpdate()" 
                                placeholder="{{ trans('general.phone') }}"
                                v-error="form.errors.has('contact_persons.' + index + '.phone')" 
                                v-error-message="form.errors.get('contact_persons.' + index + '.phone')"
                                form-group-class="mb-0"
                            />
                        </x-table.td>

                        <x-table.td class="w-10 py-2 px-2 text-right">
                            <button type="button" @click="onDeletePerson(index)" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                                <span class="material-icons-outlined text-lg">delete</span>
                            </button>
                        </x-table.td>
                    </x-table.tr>

                    <x-table.tr>
                        <x-table.td colspan="4" class="py-0 px-0">
                            <div id="person-button-add" class="border-t border-gray-200">
                                <x-button type="button" @click="onAddPerson" override="class" class="w-full h-12 flex items-center justify-center text-purple font-medium hover:bg-purple-50 transition-colors rounded-none">
                                    <span class="material-icons-outlined text-base font-bold ltr:mr-2 rtl:ml-2">add</span>

                                    {{ trans('general.form.add', ['field' => trans_choice('general.contact_persons', 1)]) }}
                                </x-button>
                            </div>
                        </x-table.td>
                    </x-table.tr>
                </x-table.tbody>
            </x-table>
        </div>
    </x-slot>
</x-form.section>
