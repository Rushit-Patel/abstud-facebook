<x-team.card title="Mock Test Details" headerClass="">

@if (isset($mockTest) && $mockTest->count() > 0)

    <div class="coaching-item  rounded-lg mb-5 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- Coaching Fields -->
            <div class="col-span-4 grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4 ">

            <input type="hidden" name="batch" id="selected_batch_multiple" value="{{ $mockTest->batch_id }}">

                <div>
                    <x-team.forms.input
                        id="name"
                        type="text"
                        name="name"
                        label="Mock Test Name"
                        :value="old('name',$mockTest->name)"
                        placeholder="Enter mock test name"
                    />
                </div>
                <!-- Mock test Date -->
                <div>
                    <x-team.forms.datepicker
                        label="Mock Test Date"
                        id="mock_test_date"
                        name="mock_test_date"
                        type="date"
                        :value="old('mock_test_date', \Carbon\Carbon::parse($mockTest->mock_test_date)->format('d-m-Y'))"
                        required
                    />
                </div>
                <div>
                    <x-team.forms.input
                        id="time"
                        type="time"
                        name="time"
                        label="Time"
                        :value="old('time', \Carbon\Carbon::parse($mockTest->mock_test_time)->format('H:i'))"
                        placeholder="Enter time"
                    />
                </div>

                @if(Auth::user()->hasPermissionTo('coaching:show-all'))
                    <div>
                        <x-team.forms.select name="branch"
                            label="Branch"
                            :options="$branch"
                            :selected="old('branch', $mockTest?->branch_id)"
                            placeholder="Select branch" required searchable="true" />
                    </div>
                @else
                    <input type="hidden" name="branch" value="{{ Auth::user()->branch_id }}">
                    <select name="branch" class="form-control" hidden>
                        <option value="{{ Auth::user()->branch_id }}" selected></option>
                    </select>
                @endif
                <div>
                    <x-team.forms.select
                        name="coaching_id"
                        label="Coaching"
                        id="coaching_select_multiple"
                        :options="$coaching"
                        :selected="old('coaching_id', $mockTest?->coaching_id)"
                        placeholder="Select coaching"
                        searchable="true"
                        required="true"
                    />
                </div>

                <!-- Batch Dropdown -->
                <div>
                    <x-team.forms.select
                        name="batch_id[]"
                        label="Batch"
                        id="batch_select_multiple"
                        :options="[]"
                        :selected="old('batch_id')"
                        placeholder="Select batch"
                        searchable="true"
                        required="true"
                        multiple="true"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <input class="kt-switch" type="checkbox" name="status" id="status"
                        value="1"
                        {{ old('status', $mockTest->status ?? 0) == 1 ? 'checked' : '' }}>
                    <label class="kt-label" for="status">Status</label>
                </div>

            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
            <div class="col-span-1">
                <div class="grid gap-5">
                    <x-team.forms.textarea id="remakrs" name="remarks" label="Remarks"
                        :value="old('remarks',$mockTest->remarks)" placeholder="Enter remarks" />
                </div>
            </div>
        </div>
    </div>

@else
    <div class="coaching-item  rounded-lg mb-5 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- Coaching Fields -->
            <div class="col-span-4 grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4 ">

                <div>
                    <x-team.forms.input
                        id="name"
                        type="text"
                        name="name"
                        label="Mock Test Name"
                        :value="old('name')"
                        placeholder="Enter mock test name"
                    />
                </div>
                <!-- Mock test Date -->
                <div>
                    <x-team.forms.datepicker
                        label="Mock Test Date"
                        id="mock_test_date"
                        name="mock_test_date"
                        type="date"
                        value="{{ old('mock_test_date') }}"
                        required
                    />
                </div>
                <div>
                    <x-team.forms.input
                        id="time"
                        type="time"
                        name="time"
                        label="Time"
                        :value="old('time')"
                        placeholder="Enter time"
                    />
                </div>

                @if(Auth::user()->hasPermissionTo('coaching:show-all'))
                    <div>
                        <x-team.forms.select name="branch" label="Branch" :options="$branch" :selected="null"
                            placeholder="Select branch" required searchable="true" />
                    </div>
                @else
                    <input type="hidden" name="branch" value="{{ Auth::user()->branch_id }}">
                    <select name="branch" class="form-control" hidden>
                        <option value="{{ Auth::user()->branch_id }}" selected></option>
                    </select>
                @endif
                <div>
                    <x-team.forms.select
                        name="coaching_id"
                        label="Coaching"
                        id="coaching_select_multiple"
                        :options="$coaching"
                        :selected="old('coaching_id')"
                        placeholder="Select coaching"
                        searchable="true"
                        required="true"
                    />
                </div>

                <!-- Batch Dropdown -->
                <div>
                    <x-team.forms.select
                        name="batch_id[]"
                        label="Batch"
                        id="batch_select_multiple"
                        :options="[]"
                        :selected="old('batch_id')"
                        placeholder="Select batch"
                        searchable="true"
                        required="true"
                        multiple="true"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <input class="kt-switch" type="checkbox" name="status" id="status"
                        value="1"
                        {{ old('status' ) ? 'checked' : '' }}>
                    <label class="kt-label" for="is_drop">Status</label>
                </div>

            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
            <div class="col-span-1">
                <div class="grid gap-5">
                    <x-team.forms.textarea id="remakrs" name="remarks" label="Remarks"
                        :value="old('remarks')" placeholder="Enter remarks" />
                </div>
            </div>
        </div>
    </div>
@endif
</x-team.card>
