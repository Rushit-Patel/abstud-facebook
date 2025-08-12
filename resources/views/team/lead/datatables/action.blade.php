<div class="flex gap-2">
    @haspermission('lead:edit')
        <a href="{{ route('team.lead.edit', $id) }}" class="kt-btn kt-btn-sm"><i class="ki-filled ki-pencil text-md"></i></a>
    @endhaspermission
    @haspermission('lead:delete')
        <button type="delete" class="kt-btn-sm kt-btn-destructive" data-kt-modal-toggle="#delete_modal" data-form_action="{{route('team.lead.destroy', $id)}}">
            <i class="ki-filled ki-trash text-md"></i>
        </button>
    @endhaspermission
</div>
