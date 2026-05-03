@extends('layouts.master')

@section('title', __('users.page.title'))

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('common.sidebar.users') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('users.page.title') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            @can('users.create')
                <button class="btn btn-primary" data-toggle="modal" data-target="#createUserModal">
                    <i class="las la-plus ml-1"></i>{{ __('users.actions.add') }}
                </button>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    @include('partials.alerts')

    <div class="row row-sm">
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">{{ __('users.summary.total') }}</h6>
                            <h2 class="mb-0">{{ $summary['total'] }}</h2>
                        </div>
                        <div class="avatar avatar-md bg-primary-transparent rounded-circle">
                            <i class="fe fe-users text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">{{ __('users.summary.active') }}</h6>
                            <h2 class="mb-0">{{ $summary['active'] }}</h2>
                        </div>
                        <div class="avatar avatar-md bg-success-transparent rounded-circle">
                            <i class="fe fe-check text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">{{ __('users.summary.inactive') }}</h6>
                            <h2 class="mb-0">{{ $summary['inactive'] }}</h2>
                        </div>
                        <div class="avatar avatar-md bg-secondary-transparent rounded-circle">
                            <i class="fe fe-user-x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title mb-1">{{ __('users.summary.suspended') }}</h6>
                            <h2 class="mb-0">{{ $summary['suspended'] }}</h2>
                        </div>
                        <div class="avatar avatar-md bg-danger-transparent rounded-circle">
                            <i class="fe fe-slash text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <p class="mb-0 text-muted">{{ __('users.page.subtitle') }}</p>
        </div>
        <div class="card-body">
            <form method="get" action="{{ route('users.index') }}" class="row row-sm mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                        placeholder="{{ __('users.filters.search') }}" value="{{ $filters['search'] }}">
                </div>
                <div class="col-md-3 mt-2 mt-md-0">
                    <select name="status" class="form-control">
                        <option value="">{{ __('users.filters.all_statuses') }}</option>
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mt-2 mt-md-0">
                    <select name="role" class="form-control">
                        <option value="">{{ __('users.filters.all_roles') }}</option>
                        @foreach ($availableRoles as $role)
                            <option value="{{ $role->name }}" @selected($filters['role'] === $role->name)>
                                {{ method_exists($role, 'resolvedDisplayName') ? $role->resolvedDisplayName() : $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mt-2 mt-md-0 d-flex">
                    <button class="btn btn-primary btn-block mr-2" type="submit">{{ __('users.filters.apply') }}</button>
                    <a href="{{ route('users.index') }}"
                        class="btn btn-light btn-block">{{ __('users.filters.reset') }}</a>
                </div>
            </form>

            <div class="table-responsive border-top userlist-table">
                <table class="table card-table table-striped table-vcenter text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('users.table.user') }}</th>
                            <th>{{ __('users.table.roles') }}</th>
                            <th>{{ __('users.table.status') }}</th>
                            <th>{{ __('users.table.created_at') }}</th>
                            <th>{{ __('users.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar avatar-md rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3 ml-3">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $user->name }}</div>
                                            <div class="text-muted">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @forelse ($user->roles as $role)
                                        <span
                                            class="badge badge-primary mr-1 ml-1">{{ method_exists($role, 'resolvedDisplayName') ? $role->resolvedDisplayName() : $role->name }}</span>
                                    @empty
                                        <span class="text-muted">-</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span
                                        class="badge badge-{{ $user->statusBadgeClass() }}">{{ $user->statusLabel() }}</span>
                                </td>
                                <td>{{ $user->created_at?->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('users.view')
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-primary"
                                                title="{{ __('users.actions.show') }}">
                                                <i class="las la-search"></i>
                                            </a>
                                        @endcan
                                        @can('users.update')
                                            <button class="btn btn-sm btn-info edit-user-button" data-toggle="modal"
                                                data-target="#editUserModal"
                                                data-update-url="{{ route('users.update', $user) }}"
                                                data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                                data-status="{{ $user->status }}"
                                                data-role-ids="{{ $user->roles->pluck('id')->join(',') }}"
                                                title="{{ __('users.actions.edit') }}">
                                                <i class="las la-pen"></i>
                                            </button>
                                        @endcan
                                        @can('users.delete')
                                            <button class="btn btn-sm btn-danger delete-user-button" data-toggle="modal"
                                                data-target="#deleteUserModal"
                                                data-delete-url="{{ route('users.destroy', $user) }}"
                                                data-name="{{ $user->name }}" title="{{ __('users.actions.delete') }}">
                                                <i class="las la-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('users.table.empty') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('users.create')
        <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('users.actions.add') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ route('users.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('users.form.name') }}</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('users.form.email') }}</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('users.form.password') }}</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('users.form.status') }}</label>
                                        <select name="status" class="form-control" required>
                                            @foreach ($statusOptions as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="d-block">{{ __('users.form.roles') }}</label>
                                @foreach ($availableRoles as $role)
                                    <label class="d-inline-flex align-items-center mr-3 ml-3 mb-2">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                            class="mr-2 ml-2">
                                        <span>{{ method_exists($role, 'resolvedDisplayName') ? $role->resolvedDisplayName() : $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">{{ __('users.actions.save') }}</button>
                            <button class="btn btn-secondary" type="button"
                                data-dismiss="modal">{{ __('users.actions.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('users.update')
        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('users.actions.edit') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" id="editUserForm">
                        @method('patch')
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('users.form.name') }}</label>
                                        <input type="text" class="form-control" id="edit_user_name" name="name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('users.form.email') }}</label>
                                        <input type="email" class="form-control" id="edit_user_email" name="email"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('users.form.password') }}</label>
                                        <input type="password" class="form-control" name="password">
                                        <small class="text-muted">{{ __('users.form.password_hint') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('users.form.status') }}</label>
                                        <select id="edit_user_status" name="status" class="form-control" required>
                                            @foreach ($statusOptions as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="d-block">{{ __('users.form.roles') }}</label>
                                @foreach ($availableRoles as $role)
                                    <label class="d-inline-flex align-items-center mr-3 ml-3 mb-2">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                            class="mr-2 ml-2 edit-role-checkbox">
                                        <span>{{ method_exists($role, 'resolvedDisplayName') ? $role->resolvedDisplayName() : $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">{{ __('users.actions.save') }}</button>
                            <button class="btn btn-secondary" type="button"
                                data-dismiss="modal">{{ __('users.actions.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('users.delete')
        <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('users.actions.confirm_delete') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" id="deleteUserForm">
                        @method('delete')
                        @csrf
                        <div class="modal-body">
                            <p>{{ __('users.messages.delete_confirmation') }}</p>
                            <input type="text" id="delete_user_name" class="form-control" readonly>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" type="submit">{{ __('users.actions.delete') }}</button>
                            <button class="btn btn-secondary" type="button"
                                data-dismiss="modal">{{ __('users.actions.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script>
        $('.edit-user-button').on('click', function() {
            const button = $(this);
            const selectedRoleIds = (button.data('role-ids') || '').toString().split(',').filter(Boolean);

            $('#editUserForm').attr('action', button.data('update-url'));
            $('#edit_user_name').val(button.data('name'));
            $('#edit_user_email').val(button.data('email'));
            $('#edit_user_status').val(button.data('status'));

            $('.edit-role-checkbox').prop('checked', false);

            selectedRoleIds.forEach(function(roleId) {
                $('.edit-role-checkbox[value="' + roleId + '"]').prop('checked', true);
            });
        });

        $('.delete-user-button').on('click', function() {
            const button = $(this);

            $('#deleteUserForm').attr('action', button.data('delete-url'));
            $('#delete_user_name').val(button.data('name'));
        });
    </script>
@endsection
