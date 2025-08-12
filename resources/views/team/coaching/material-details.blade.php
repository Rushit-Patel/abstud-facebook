@if(count($materials) > 0)
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4"> {{-- 3 columns layout --}}
        @foreach($materials as $material)
            <div> {{-- Each checkbox inside one grid cell --}}
                <x-team.forms.checkbox
                    name="materials[]"
                    id="material_{{ $material->id }}"
                    :value="$material->id"
                    for="material_{{ $material->id }}"
                    :label="$material->name"
                    style="inline"
                    class="test-checkbox"
                    :checked="in_array($material->id, $selectedMaterials)"
                />
            </div>
        @endforeach
    </div>
@else
    <p>No materials found for selected coaching.</p>
@endif
