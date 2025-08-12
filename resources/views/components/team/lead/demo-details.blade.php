<x-team.card title="Demo Details" headerClass="">

@if (isset($getDemoData) && $getDemoData->count() > 0)
    <div class="demo-item  rounded-lg mb-5 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- Demo Fields -->
            <div class="col-span-4 grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4 ">
                <!-- Demo  -->
                <input type="hidden" name="client_lead_id" value="{{ $getDemoData->client_lead_id }}">
                <input type="hidden" name="demo_id" value="{{ $getDemoData->id }}">
                <input type="hidden" name="batch" id="selected_batch" value="{{ $getDemoData->batch_id }}">
                <div>
                    <x-team.forms.select
                        name="coaching"
                        label="Coaching"
                        id="coaching_select"
                        :options="$coaching"
                        :selected="old('coaching' ,$getDemoData->coaching_id)"
                        placeholder="Select coaching"
                        searchable="true"
                        required="true"
                    />
                </div>

                <!-- Batch Dropdown -->
                <div>
                    <x-team.forms.select
                        name="batch"
                        label="Batch"
                        id="batch_select"
                        :options="[]"
                        :selected="old('batch' ,$getDemoData->batch_id)"
                        placeholder="Select batch"
                        searchable="true"
                        required="true"
                    />
                </div>


                <!-- Demo Date -->
                <div>
                    <x-team.forms.datepicker
                        label="Demo Date"
                        id="demo_date"
                        name="demo_date"
                        type="date"
                        :value=" old('demo_date', \Carbon\Carbon::parse($getDemoData->demo_date)->format('d-m-Y'))"
                        required
                    />
                </div>

                <div>
                    <x-team.forms.select
                        name="assign_owner"
                        label="Assign Owner"
                        :options="$user"
                        :selected="old('assign_owner',$getDemoData->assign_owner)"
                        placeholder="Select assign owner"
                        searchable="true"
                        required="true"
                    />
                </div>

                <div>
                    @php
                        $status = [
                            '0' => 'Demo Pending',
                            '1' => 'Demo Attended',
                            '2' => 'Demo Cancelled'
                        ];
                    @endphp

                    <x-team.forms.select
                        name="status"
                        label="Status"
                        :options="$status"
                        :selected="old('status', $getDemoData->status ?? '')"
                        placeholder="Select status"
                        searchable="true"
                        required="true"
                    />
                </div>



            </div>
        </div>
    </div>
@else
    <div class="demo-item  rounded-lg mb-5 relative bg-secondary-50">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
            <!-- Demo Fields -->
            <div class="col-span-4 grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4 ">
                <!-- Demo  -->
                <input type="hidden" name="client_lead_id" value="{{ $client_lead_id }}">
                <div>
                    <x-team.forms.select
                        name="coaching"
                        label="Coaching"
                        id="coaching_select"
                        :options="$coaching"
                        :selected="old('coaching')"
                        placeholder="Select coaching"
                        searchable="true"
                        required="true"
                    />
                </div>

                <!-- Batch Dropdown -->
                <div>
                    <x-team.forms.select
                        name="batch"
                        label="Batch"
                        id="batch_select"
                        :options="[]"
                        placeholder="Select batch"
                        searchable="true"
                        required="true"
                    />
                </div>


                <!-- Demo Date -->
                <div>
                    <x-team.forms.datepicker
                        label="Demo Date"
                        id="demo_date"
                        name="demo_date"
                        type="date"
                        value="{{ old('demo_date') }}"
                        required
                    />
                </div>

                <div>
                    <x-team.forms.select
                        name="assign_owner"
                        label="Assign Owner"
                        :options="$user"
                        :selected="old('assign_owner')"
                        placeholder="Select assign owner"
                        searchable="true"
                        required="true"
                    />
                </div>

            </div>
        </div>
    </div>
@endif
</x-team.card>
