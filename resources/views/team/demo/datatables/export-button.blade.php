<form method="POST" action="{{ route('team.demo.export') }}">
    @csrf
    @method('POST')
    <input type="hidden" name="date" id="filter_date">
    <input type="hidden" name="branch" id="filter_branch">
    <input type="hidden" name="owner" id="filter_owner">
    <input type="hidden" name="demo_name" value="{{ $DemoName }}">
    <input type="hidden" name="branch_dashboard" value="{{ request('branch') }}">
    <input type="hidden" name="date_dashboard" value="{{ request('date') }}">

    <button type="submit" class="kt-btn kt-btn-outline justify-center">
        <i class="ki-filled ki-folder-down"></i> Export
    </button>
</form>
