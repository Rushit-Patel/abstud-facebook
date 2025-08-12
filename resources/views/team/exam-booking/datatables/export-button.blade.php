<form method="POST" action="{{ route('team.exam-booking.export') }}">
    @csrf
    @method('POST')
    <input type="hidden" name="exam_date" id="filter_exam_date">
    <input type="hidden" name="result_date" id="filter_result_date">
    <input type="hidden" name="branch" id="filter_branch">
    <input type="hidden" name="coaching" id="filter_coaching">
    <input type="hidden" name="batch" id="filter_batch">

    <button type="submit" class="kt-btn kt-btn-outline justify-center">
        <i class="ki-filled ki-folder-down"></i> Export
    </button>
</form>
