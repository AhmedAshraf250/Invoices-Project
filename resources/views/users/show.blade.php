@extends('layouts.master')

@section('title', __('users.page.details'))

@section('css')
    <link href="{{ URL::asset('assets/plugins/treeview/treeview-rtl.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('users.page.details') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ $user->name }}</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <a href="{{ route('users.index') }}" class="btn btn-light ml-2">
                <i class="las la-arrow-right ml-1"></i>{{ __('users.actions.back') }}
            </a>
            @can('users.update')
                <button class="btn btn-info" data-toggle="modal" data-target="#editUserModal">
                    <i class="las la-pen ml-1"></i>{{ __('users.actions.quick_edit') }}
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
                <div class="card-body text-center">
                    <div class="avatar avatar-xxl rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <span class="badge badge-{{ $user->statusBadgeClass() }}">{{ $user->statusLabel() }}</span>
                    @if ($user->isProtected())
                        <p class="text-muted small mt-3 mb-0">{{ __('users.messages.protected_status_hint') }}</p>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">{{ __('users.details.roles') }}</h6>
                    @forelse ($user->roles as $role)
                        <span class="badge badge-primary mr-1 ml-1 mb-2">{{ method_exists($role, 'resolvedDisplayName') ? $role->resolvedDisplayName() : $role->name }}</span>
                    @empty
                        <p class="text-muted mb-0">-</p>
                    @endforelse
                    <hr>
                    <p class="mb-1"><strong>{{ __('users.details.email') }}:</strong> {{ $user->email }}</p>
                    <p class="mb-0"><strong>{{ __('users.details.created_at') }}:</strong> {{ $user->created_at?->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">{{ __('users.details.permissions') }}</h6>
                    <ul id="user-permissions-tree">
                        @include('permissions.partials.tree-items', ['permissionGroups' => $permissionGroups])
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @can('users.update')
        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('users.actions.quick_edit') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" action="{{ route('users.update', $user) }}">
                        @method('patch')
                        @csrf
                        <input type="hidden" name="return_to_show" value="1">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('users.form.name') }}</label>
                                        <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('users.form.email') }}</label>
                                        <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
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
                                        @if ($user->isProtected())
                                            <input type="hidden" name="status" value="{{ $user->status }}">
                                            <input type="text" class="form-control" value="{{ $user->statusLabel() }}" readonly>
                                            <small class="text-muted">{{ __('users.messages.protected_status_hint') }}</small>
                                        @else
                                            <select name="status" class="form-control" required>
                                                @foreach ($statusOptions as $value => $label)
                                                    <option value="{{ $value }}" @selected($user->status === $value)>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="d-block">{{ __('users.form.roles') }}</label>
                                @foreach ($availableRoles as $role)
                                    <label class="d-inline-flex align-items-center mr-3 ml-3 mb-2">
                                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="mr-2 ml-2"
                                            @checked($user->roles->contains('id', $role->id))>
                                        <span>{{ method_exists($role, 'resolvedDisplayName') ? $role->resolvedDisplayName() : $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit">{{ __('users.actions.save') }}</button>
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">{{ __('users.actions.cancel') }}</button>
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
