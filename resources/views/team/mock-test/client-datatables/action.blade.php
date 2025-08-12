<div class="flex gap-2">
    @haspermission('coaching:edit')
        <a href="{{ route('team.mock-test-client.show.result', [$id, $client_coaching]) }}" class="kt-btn kt-btn-sm">
            <i class="ki-filled ki-pencil text-md"></i>
        </a>
    @endhaspermission
</div>
