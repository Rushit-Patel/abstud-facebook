<div class="flex gap-2">
    @haspermission('coaching:create')
        <a href="{{ route('team.mock-test-client.show', $id) }}" class="kt-btn kt-btn-sm"><i class="ki-filled ki-plus text-md"></i>Add Client</a>
    @endhaspermission
</div>
