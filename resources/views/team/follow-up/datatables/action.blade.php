<div class="flex items-center gap-2">
    <button 
        class="open-followup-modal kt-btn kt-btn-sm kt-btn-primary"
        data-url="{{ route('team.lead-follow-up.edit', $id) }}"
        data-action="{{ route('team.lead-follow-up.update', $id) }}">
        <i class="ki-filled ki-notepad-edit text-sm"></i>
        Complete
    </button>
</div>
