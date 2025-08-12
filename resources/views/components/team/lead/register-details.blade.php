<x-team.card title="Registration Details" headerClass="">

@if (isset($regDetails) && $regDetails->count() > 0)
    <div class="demo-item rounded-lg mb-5 relative bg-secondary-50">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4">
        <!-- Hidden Inputs -->
        <input type="hidden" name="client_lead_id" value="{{ $regDetails->client_lead_id }}">
        <input type="hidden" name="client_reg_id" value="{{ $regDetails->id }}">

        <!-- Register Date -->
        <div>
            <x-team.forms.datepicker
                label="Register Date"
                id="reg_date"
                name="reg_date"
                type="date"
                :value=" old('reg_date', \Carbon\Carbon::parse($regDetails->reg_date)->format('d-m-Y'))"
                required
            />
        </div>

        <!-- Purpose -->
        <div>
            <x-team.forms.select
                name="purpose"
                label="Purpose"
                :options="$purposes"
                :selected="old('purpose', $regDetails->purpose)"
                placeholder="Select purpose"
                required="true"
                searchable="true"
                id="purposeSelect"
                inputId="purposeSelect"
                class="purpose-select"
            />
        </div>

        <!-- Country -->
        <div id="countryDiv" style="display:none;">
            <x-team.forms.select
                name="country"
                label="Country"
                :options="$country"
                :selected="old('country',$regDetails->country)"
                placeholder="Select country"
                searchable="true"
                required="true"
            />
        </div>

        <!-- Coaching -->
        <div id="coachingDiv" style="display:none;">
            <x-team.forms.select
                name="coaching"
                label="Coaching"
                :options="$coaching"
                :selected="old('coaching',$regDetails->coaching)"
                placeholder="Select coaching"
                searchable="true"
                required="true"
            />
        </div>


        <!-- Assign Owner -->
        <div>
            <x-team.forms.select
                name="assign_owner"
                label="Assign Owner"
                :options="$user"
                :selected="old('assign_owner',$regDetails->assign_owner)"
                placeholder="Select assign owner"
                searchable="true"
                required="true"
            />
        </div>


        <!-- Status -->
        <div>
            <x-team.forms.select
                name="sub_status"
                label="Status"
                :options="$subStatus"
                :selected="old('sub_status',$regDetails->clientLead->sub_status)"
                placeholder="Select status"
                searchable="true"
                required="true"
            />
        </div>
    </div>
</div>

@else
    <div class="demo-item rounded-lg mb-5 relative bg-secondary-50">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-4">

        <!-- Register Date -->
        <input type="hidden" name="client_lead_id" value="{{ $client_lead_id }}">
        <div>
            <x-team.forms.datepicker
                label="Register Date"
                id="reg_date"
                name="reg_date"
                type="date"
                value="{{ old('reg_date') }}"
                required
            />
        </div>

        <!-- Purpose -->
        <div>
            <x-team.forms.select
                name="purpose"
                label="Purpose"
                :options="$purposes"
                :selected="old('purpose', $purposes->keys()->first())"
                placeholder="Select purpose"
                required="true"
                searchable="true"
                id="purposeSelect"
                inputId="purposeSelect"
                class="purpose-select"
            />
        </div>

        <!-- Country -->
        <div id="countryDiv" style="display:none;">
            <x-team.forms.select
                name="country"
                label="Country"
                :options="$country"
                :selected="old('country')"
                placeholder="Select country"
                searchable="true"
                required="true"
            />
        </div>

        <!-- Coaching -->
        <div id="coachingDiv" style="display:none;">
            <x-team.forms.select
                name="coaching"
                label="Coaching"
                :options="$coaching"
                :selected="old('coaching')"
                placeholder="Select coaching"
                searchable="true"
                required="true"
            />
        </div>

        <!-- Assign Owner -->
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

        <!-- Status -->
        <div>
            <x-team.forms.select
                name="sub_status"
                label="Status"
                :options="$subStatus"
                :selected="old('sub_status')"
                placeholder="Select status"
                searchable="true"
                required="true"
            />
        </div>

    </div>
</div>

@endif
</x-team.card>
