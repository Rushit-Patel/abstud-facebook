<div class="border-b border-gray-200 rounded-lg mb-4 pb-4">
    @if (isset($relativeData) && $relativeData->count() > 0)
        <x-team.lead.relative-foreign-country
            :typeOfRelations="$typeOfRelations"
            :countrys="$countrys"
            :otherVisaTypes="$otherVisaTypes"
            :relativeData="$relativeData"
        />
    @else
        <x-team.lead.relative-foreign-country
            :typeOfRelations="$typeOfRelations"
            :countrys="$countrys"
            :otherVisaTypes="$otherVisaTypes"
        />
    @endif
</div>
<div class="border-b border-gray-200 rounded-lg mb-4 pb-4">
    @if (isset($rejectionData) && $rejectionData->count() > 0)
        <x-team.lead.visa-rejection-details
            :visaRejectionCountry="$countrys"
            :visaRejectionVisaType="$otherVisaTypes"
            :visaRejectionDatas="$rejectionData"
        />
    @else
        <x-team.lead.visa-rejection-details
            :visaRejectionCountry="$countrys"
            :visaRejectionVisaType="$otherVisaTypes"
        />
    @endif
</div>
<div class="border-b border-gray-200 rounded-lg mb-4 pb-4">
    @if (isset($visitedData) && $visitedData->count() > 0)
        <x-team.lead.visited-details
            :visitedCountry="$countrys"
            :visitedVisaType="$otherVisaTypes"
            :visitedDataDatas="$visitedData"
        />
    @else
        <x-team.lead.visited-details
            :visitedCountry="$countrys"
            :visitedVisaType="$otherVisaTypes"
        />
    @endif
</div>

