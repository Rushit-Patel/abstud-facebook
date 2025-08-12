<form method="POST" action="{{ route('team.leads.export') }}">
    @csrf
    @method('POST')
    <input type="hidden" name="date" id="filter_date">
    <input type="hidden" name="status" id="filter_status">
    <input type="hidden" name="branch" id="filter_branch">
    <input type="hidden" name="owner" id="filter_owner">
    <input type="hidden" name="source" id="filter_source">
    <input type="hidden" name="lead_type" id="filter_lead_type">
    <input type="hidden" name="status_dashboard" value="{{ base64_decode(request('status')) }}">
    <input type="hidden" name="branch_dashboard" value="{{ request('branch') }}">
    <input type="hidden" name="purpose_dashboard" value="{{ base64_decode(request('purpose')) }}">
    <input type="hidden" name="date_dashboard" value="{{ request('date') }}">

    <button type="submit" class="kt-btn kt-btn-outline justify-center">
        <i class="ki-filled ki-folder-down"></i> Export
    </button>
</form>
