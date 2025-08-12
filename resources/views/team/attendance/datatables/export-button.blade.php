<form method="POST" action="{{ route('team.attendance.export') }}">
    @csrf
    @method('POST')
    <input type="hidden" name="branch" id="filter_branch">
    <input type="hidden" name="coaching" id="filter_coaching">
    <input type="hidden" name="batch_id" id="filter_batch_id">

    <button type="submit" class="kt-btn kt-btn-outline justify-center">
        <i class="ki-filled ki-folder-down"></i> Export
    </button>
</form>
