@extends('layouts.master')

@section('title', __('roles.page.title'))

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('common.sidebar.users') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('roles.page.title') }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            @can('roles.create')
                <button class="btn btn-primary" data-toggle="modal" data-target="#createRoleModal">
                    <i class="las la-plus ml-1"></i>{{ __('roles.actions.add') }}
                </button>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    @include('partials.alerts')

    <div class="card">
        <div class="card-header">
            <p class="mb-0 text-muted">{{ __('roles.page.subtitle') }}</p>
        </div>
        <div class="card-body">
            <div class="table-responsive border-top userlist-table">
                <table class="table card-table table-striped table-vcenter text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('roles.table.name') }}</th>
                            <th class="text-center">{{ __('roles.table.users_count') }}</th>
                            <th class="text-center">{{ __('roles.table.permissions_count') }}</th>
                            <th class="text-center">{{ __('roles.table.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div
                                            class="avatar avatar-md bg-primary-transparent rounded-circle d-flex align-items-center justify-content-center mr-3 ml-3">
                                            <i class="las la-user-shield tx-20 text-primary"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $role->resolvedDisplayName() }}</div>
                                            <div class="text-muted tx-12">{{ $role->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-pill badge-light">{{ $role->users_count }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-pill badge-light">{{ $role->permissions_count }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    @can('roles.view')
                                        <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-primary"
                                            title="{{ __('roles.actions.show') }}">
                                            <i class="las la-search"></i>
                                        </a>
                                    @endcan
                                    @can('update', $role)
                                        <button class="btn btn-sm btn-info edit-role-button" data-toggle="modal"
                                            data-target="#editRoleModal" data-update-url="{{ route('roles.update', $role) }}"
                                            data-name="{{ $role->name }}"
                                            data-display-name-ar="{{ $role->getTranslation('display_name', 'ar', false) }}"
                                            data-display-name-en="{{ $role->getTranslation('display_name', 'en', false) }}"
                                            data-permission-ids="{{ $role->permissions->pluck('id')->join(',') }}"
                                            title="{{ __('roles.actions.edit') }}">
                                            <i class="las la-pen"></i>
                                        </button>
                                    @endcan
                                    @can('delete', $role)
                                        <button class="btn btn-sm btn-danger delete-role-button" data-toggle="modal"
                                            data-target="#deleteRoleModal"
                                            data-delete-url="{{ route('roles.destroy', $role) }}"
                                            data-name="{{ $role->name }}" title="{{ __('roles.actions.delete') }}">
                                            <i class="las la-trash"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @can('roles.create')
        <div class="modal fade" id="createRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('roles.actions.add') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form method="post" action="{{ route('roles.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>{{ __('roles.form.name') }}</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('roles.form.display_name_ar') }}</label>
                                        <input type="text" class="form-control" name="display_name_ar" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('roles.form.display_name_en') }}</label>
                                        <input type="text" class="form-control" name="display_name_en" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @include('permissions.partials.grouped-checkboxes', [
                                    'groupedPermissions' => $groupedPermissions,
                                    'selectedPermissions' => [],
                                    'inputClass' => 'create-role-permission-checkbox',
                                ])
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">{{ __('roles.actions.save') }}</button>
                            <button class="btn btn-secondary" type="button"
                                data-dismiss="modal">{{ __('roles.actions.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('roles.update')
        <div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('roles.actions.edit') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form method="post" id="editRoleForm">
                        @method('patch')
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>{{ __('roles.form.name') }}</label>
                                <input type="text" class="form-control" id="edit_role_name" name="name" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('roles.form.display_name_ar') }}</label>
                                        <input type="text" class="form-control" id="edit_role_display_name_ar"
                                            name="display_name_ar" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('roles.form.display_name_en') }}</label>
                                        <input type="text" class="form-control" id="edit_role_display_name_en"
                                            name="display_name_en" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @include('permissions.partials.grouped-checkboxes', [
                                    'groupedPermissions' => $groupedPermissions,
                                    'selectedPermissions' => [],
                                    'inputClass' => 'edit-role-permission-checkbox',
                                ])
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">{{ __('roles.actions.save') }}</button>
                            <button class="btn btn-secondary" type="button"
                                data-dismiss="modal">{{ __('roles.actions.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('roles.delete')
        <div class="modal fade" id="deleteRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('roles.actions.delete') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form method="post" id="deleteRoleForm">
                        @method('delete')
                        @csrf
                        <div class="modal-body">
                            <input type="text" class="form-control" id="delete_role_name" readonly>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" type="submit">{{ __('roles.actions.delete') }}</button>
                            <button class="btn btn-secondary" type="button"
                                data-dismiss="modal">{{ __('roles.actions.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script>
        $('.edit-role-button').on('click', function() {
            const button = $(this);
            const selectedPermissionIds = (button.data('permission-ids') || '').toString().split(',').filter(
                Boolean);

            $('#editRoleForm').attr('action', button.data('update-url'));
            $('#edit_role_name').val(button.data('name'));
            $('#edit_role_display_name_ar').val(button.data('display-name-ar'));
            $('#edit_role_display_name_en').val(button.data('display-name-en'));
            $('.edit-role-permission-checkbox').prop('checked', false);

            selectedPermissionIds.forEach(function(permissionId) {
                $('.edit-role-permission-checkbox[value="' + permissionId + '"]').prop('checked', true);
            });
        });

        $('.delete-role-button').on('click', function() {
            const button = $(this);

            $('#deleteRoleForm').attr('action', button.data('delete-url'));
            $('#delete_role_name').val(button.data('name'));
        });
    </script>
@endsection
