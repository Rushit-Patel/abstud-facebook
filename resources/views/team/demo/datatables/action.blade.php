<div class="flex gap-2">
    @haspermission('demo:edit')
        <a href="{{ route('team.demo.edit', $id) }}" class="kt-btn kt-btn-sm"><i class="ki-filled ki-pencil text-md"></i></a>
    @endhaspermission
    @haspermission('demo:delete')
        <button type="delete" class="kt-btn-sm kt-btn-destructive" data-kt-modal-toggle="#delete_modal" data-form_action="{{route('team.demo.destroy', $id)}}">
            <i class="ki-filled ki-trash text-md"></i>
        </button>
    @endhaspermission
</div>
