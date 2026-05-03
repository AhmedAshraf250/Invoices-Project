@foreach ($permissionGroups as $group)
    <li>
        <a href="#">{{ $group['label'] }}</a>
        <ul>
            @foreach ($group['permissions'] as $permission)
                <li>
                    <span>{{ $permission->resolved_display_name }}</span>
                    <small class="d-block text-muted">{{ $permission->name }}</small>
                </li>
            @endforeach
        </ul>
    </li>
@endforeach
