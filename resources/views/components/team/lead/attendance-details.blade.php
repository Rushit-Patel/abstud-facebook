<x-team.card title="Coaching Details" headerClass="">
    <div class="coaching-item rounded-lg mb-5 relative bg-secondary-50 p-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 items-end">

            <!-- Date -->
            <div>
                <x-team.forms.datepicker
                    label="Search Date"
                    id="joining_date"
                    name="joining_date"
                    type="date"
                    value="{{ old('joining_date') }}"
                />
            </div>

            <!-- Coaching -->
            <div>
                <x-team.forms.select
                    name="coaching_id[]"
                    label="Coaching"
                    id="coaching_select_multiple"
                    :options="$coaching"
                    :selected="old('coaching_id', [])"
                    placeholder="Select coaching"
                    searchable="true"
                    multiple="true"
                />
            </div>

            <!-- Batch -->
            <div>
                <x-team.forms.select
                    name="batch_id[]"
                    label="Batch"
                    id="batch_select_multiple"
                    :options="[]"
                    :selected="old('batch_id', [])"
                    placeholder="Select batch"
                    searchable="true"
                    multiple="true"
                />
            </div>

            <!-- Search Button -->
            <div>
                <button id="searchBtn"
                    type="button"
                    class="w-full px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg">
                    Search
                </button>
            </div>

        </div>
    </div>

    <div id="searchResults" class="mt-5"></div>

</x-team.card>
