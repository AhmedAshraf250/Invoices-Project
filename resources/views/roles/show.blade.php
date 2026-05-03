@extends('layouts.master')

@section('title', __('roles.page.details'))

@section('css')
    <link href="{{ URL::asset('assets/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/treeview/treeview-rtl.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('roles.page.details') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ $role->resolvedDisplayName() }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content flex-wrap">
            <a href="{{ route('roles.index') }}" class="btn btn-secondary mb-2 mb-xl-0 ml-2">
                <i class="las la-arrow-right ml-1"></i>{{ __('roles.actions.back') }}
            </a>
            @can('roles.create')
                <a href="{{ route('roles.index') }}" class="btn btn-primary mb-2 mb-xl-0 ml-2">
                    <i class="las la-plus ml-1"></i>{{ __('roles.actions.add') }}
                </a>
            @endcan
            @can('update', $role)
                <button class="btn btn-info mb-2 mb-xl-0 ml-2" data-toggle="modal" data-target="#editRoleModal">
                    <i class="las la-pen ml-1"></i>{{ __('roles.actions.edit') }}
                </button>
            @endcan
            @can('delete', $role)
                <button class="btn btn-danger mb-2 mb-xl-0" data-toggle="modal" data-target="#deleteRoleModal">
                    <i class="las la-trash ml-1"></i>{{ __('roles.actions.delete') }}
                </button>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    @include('partials.alerts')

    <div class="row row-sm">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div
                            class="avatar avatar-xxl rounded-circle bg-primary-transparent d-flex align-items-center justify-content-center ml-3 mr-3">
                            <i class="las la-user-shield tx-30 text-primary"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">{{ $role->resolvedDisplayName() }}</h4>
                            <p class="text-muted mb-0">{{ $role->name }}</p>
                        </div>
                    </div>

                    <h6 class="mb-3">{{ __('roles.details.overview') }}</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ __('roles.table.users_count') }}</span>
                        <span class="badge badge-primary">{{ $role->users->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ __('roles.table.permissions_count') }}</span>
                        <span class="badge badge-success">{{ $role->permissions->count() }}</span>
                    </div>

                    <hr>

                    <h6 class="mb-3">{{ __('roles.details.quick_actions') }}</h6>
                    <div class="d-flex flex-wrap">
                        <a href="#role-permissions-card" class="btn btn-outline-primary btn-sm ml-2 mb-2">
                            <i class="las la-key ml-1"></i>{{ __('roles.actions.view_permissions') }}
                        </a>
                        <a href="#role-users-card" class="btn btn-outline-info btn-sm ml-2 mb-2">
                            <i class="las la-users ml-1"></i>{{ __('roles.actions.view_users') }}
                        </a>
                        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                            <i class="las la-list ml-1"></i>{{ __('roles.actions.back') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card" id="role-users-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">{{ __('roles.details.assigned_users') }}</h6>
                        <span class="badge badge-light">{{ $role->users->count() }}</span>
                    </div>
                    @forelse ($role->users as $user)
                        <div class="border rounded px-3 py-2 mb-2">
                            <div class="font-weight-bold">{{ $user->name }}</div>
                            <small class="text-muted d-block">{{ $user->email }}</small>
                            <span
                                class="badge badge-{{ $user->statusBadgeClass() }} mt-2">{{ $user->statusLabel() }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('roles.details.users_empty') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card" id="role-permissions-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">{{ __('roles.details.permissions') }}</h6>
                        <span class="badge badge-light">{{ $role->permissions->count() }}</span>
                    </div>
                    @if (count($permissionGroups) > 0)
                        <ul id="role-permission-tree">
                            @include('permissions.partials.tree-items', [
                                'permissionGroups' => $permissionGroups,
                            ])
                        </ul>
                    @else
                        <p class="text-muted mb-0">{{ __('roles.details.empty_permissions') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @can('update', $role)
        <div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('roles.actions.edit') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form method="post" action="{{ route('roles.update', $role) }}">
                        @method('patch')
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>{{ __('roles.form.name') }}</label>
                                <input type="text" class="form-control" name="name" value="{{ $role->name }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('roles.form.display_name_ar') }}</label>
                                        <input type="text" class="form-control" name="display_name_ar"
                                            value="{{ $role->getTranslation('display_name', 'ar', false) }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('roles.form.display_name_en') }}</label>
                                        <input type="text" class="form-control" name="display_name_en"
                                            value="{{ $role->getTranslation('display_name', 'en', false) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @include('permissions.partials.grouped-checkboxes', [
                                    'groupedPermissions' => $groupedPermissions,
                                    'selectedPermissions' => $role->permissions->pluck('id')->all(),
                                    'inputClass' => 'role-show-permission-checkbox',
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

    @can('delete', $role)
        <div class="modal fade" id="deleteRoleModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('roles.actions.delete') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form method="post" action="{{ route('roles.destroy', $role) }}">
                        @method('delete')
                        @csrf
                        <div class="modal-body">
                            <input type="text" class="form-control" value="{{ $role->resolvedDisplayName() }}" readonly>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" type="submit"
                                @disabled($role->users->isNotEmpty())>{{ __('roles.actions.delete') }}</button>
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
    <script src="{{ URL::asset('assets/plugins/treeview/treeview.js') }}"></script>
@endsection
