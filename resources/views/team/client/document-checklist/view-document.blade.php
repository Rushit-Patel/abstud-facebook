@if($documentChecklist && $documentChecklist->documentUploads && count($documentChecklist->documentUploads) > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">#</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">File Name</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">Updated Date</th>
                    <th class="px-4 py-2 text-left font-medium text-gray-600">View</th>

                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($documentChecklist->documentUploads as $index => $upload)
                    <tr>
                        <td class="px-4 py-2 text-gray-800">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $upload->document_name }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $upload->created_at ? $upload->created_at->format('d-m-Y H:i') : '-' }}</td>
                        <td class="px-4 py-2 text-gray-800">
                            @if(!empty($upload->document_path))
                                <a href="{{ asset('storage/' . $upload->document_path) }}"
                                target="_blank"
                                class="kt-btn kt-btn-sm kt-btn-primary"
                                title="View Document">
                                <i class="ki-filled ki-eye"></i>
                                </a>
                            @else
                                <span class="text-muted">No file</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-muted">No documents uploaded yet.</p>
@endif
