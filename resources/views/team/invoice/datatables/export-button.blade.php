<form method="POST" action="{{ route('team.invoice.export') }}">
    @csrf
    @method('POST')
    <input type="hidden" name="date" id="filter_date">
    <input type="hidden" name="branch" id="filter_branch">
    <input type="hidden" name="owner" id="filter_owner">
    <input type="hidden" name="invoice_name" value="{{ $invoiceName }}">

    <button type="submit" class="kt-btn kt-btn-outline justify-center">
        <i class="ki-filled ki-folder-down"></i> Export
    </button>
</form>
