@foreach ($groupedPermissions as $group)
    <div class="col-lg-6 mb-3">
        <div class="card h-100 shadow-none border">
            <div class="card-header pb-2">
                <h6 class="mb-0">{{ $group['label'] }}</h6>
            </div>
            <div class="card-body">
                @foreach ($group['permissions'] as $permission)
                    <label class="d-flex align-items-start mb-3">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                            class="mt-1 permission-checkbox {{ $inputClass ?? '' }}"
                            @checked(in_array($permission->id, $selectedPermissions ?? [], true))>
                        <span class="mr-2 ml-2">
                            <span class="d-block font-weight-bold">{{ $permission->resolved_display_name }}</span>
                            <small class="text-muted">{{ $permission->name }}</small>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
@endforeach
