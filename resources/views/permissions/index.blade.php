@extends('layouts.master')

@section('title', __('permissions.page.title'))

@section('css')
    <link href="{{ URL::asset('assets/plugins/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/treeview/treeview-rtl.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .permission-preview-shell {
            background: linear-gradient(135deg, rgba(0, 123, 255, 0.08), rgba(40, 167, 69, 0.08));
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 24px;
            overflow: hidden;
        }

        .permission-preview-hero {
            background: linear-gradient(135deg, #0f172a, #1e3a8a 58%, #0f766e);
            color: #fff;
            padding: 2rem;
        }

        .permission-preview-hero p {
            color: rgba(255, 255, 255, 0.78);
            max-width: 720px;
        }

        .permission-preview-stat {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 18px;
            padding: 1rem 1.25rem;
            backdrop-filter: blur(4px);
        }

        .permission-preview-stat span {
            display: block;
            color: rgba(255, 255, 255, 0.72);
            font-size: 0.85rem;
        }

        .permission-preview-stat strong {
            display: block;
            margin-top: 0.35rem;
            font-size: 1.75rem;
            line-height: 1;
        }

        .permission-preview-content {
            padding: 1.5rem;
        }

        .permission-preview-panel {
            background: #fff;
            border: 1px solid #e9edf4;
            border-radius: 20px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
            height: 100%;
            padding: 1.5rem;
        }

        .permission-preview-panel h5 {
            font-weight: 700;
        }

        .permission-preview-panel p {
            color: #6b7280;
            margin-bottom: 0;
        }

        .permission-preview-tree.tree {
            margin-bottom: 0;
        }

        .permission-preview-tree li {
            margin: 0.45rem 0;
        }

        .permission-preview-tree .branch {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 1rem 3.5rem 1rem 1rem;
            position: relative;
        }

        .permission-preview-tree .branch > i {
            align-items: center;
            background: #fff;
            border: 1px solid #dbe3ef;
            border-radius: 999px;
            color: #0f172a;
            display: inline-flex;
            float: none;
            font-size: 0.85rem;
            height: 1.7rem;
            justify-content: center;
            margin: 0;
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.7rem;
            z-index: 2;
        }

        .permission-preview-tree .branch > button {
            background: transparent;
            color: #0f172a;
            display: block;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            padding-left: 0.25rem;
            padding-right: 0;
            text-align: right;
            width: 100%;
        }

        .permission-preview-tree .branch > button:hover {
            color: #1d4ed8;
        }

        .permission-preview-tree .permission-group-meta {
            color: #64748b;
            display: block;
            font-size: 0.85rem;
            margin-top: 0.35rem;
            padding-left: 0.25rem;
        }

        .permission-preview-tree ul li:last-child::before {
            background: #f8fafc;
        }

        .permission-preview-tree .permission-item {
            align-items: center;
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 16px;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: space-between;
            padding: 0.95rem 1rem;
        }

        .permission-preview-tree .permission-item strong {
            color: #111827;
            display: block;
            font-size: 0.95rem;
        }

        .permission-preview-tree .permission-item code {
            background: #f8fafc;
            border-radius: 999px;
            color: #475569;
            font-size: 0.78rem;
            padding: 0.2rem 0.55rem;
        }

        .permission-preview-tree .permission-item-badge {
            background: linear-gradient(135deg, #dbeafe, #dcfce7);
            border-radius: 999px;
            color: #0f172a;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.4rem 0.75rem;
            white-space: nowrap;
        }

        .permission-preview-note {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.08), rgba(16, 185, 129, 0.08));
            border: 1px dashed rgba(59, 130, 246, 0.25);
            border-radius: 18px;
            padding: 1rem 1.1rem;
        }
    </style>
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ __('common.sidebar.users') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ __('permissions.page.title') }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @include('partials.alerts')

    <div class="permission-preview-shell">
        <div class="permission-preview-hero">
            <div class="d-flex flex-column flex-xl-row align-items-xl-end justify-content-between">
                <div class="mb-4 mb-xl-0">
                    <div class="text-uppercase tx-12 font-weight-bold mb-2">{{ __('permissions.page.title') }}</div>
                    <h2 class="mb-3">{{ __('permissions.preview.heading') }}</h2>
                    <p class="mb-0">{{ __('permissions.preview.description') }}</p>
                </div>
                <div class="row row-sm w-100 justify-content-xl-end">
                    <div class="col-sm-6 col-xl-4">
                        <div class="permission-preview-stat">
                            <span>{{ __('permissions.preview.stats.groups') }}</span>
                            <strong>{{ $summary['groups_count'] }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-4 mt-3 mt-sm-0">
                        <div class="permission-preview-stat">
                            <span>{{ __('permissions.preview.stats.permissions') }}</span>
                            <strong>{{ $summary['permissions_count'] }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="permission-preview-content">
            <div class="row row-sm">
                <div class="col-xl-4">
                    <div class="permission-preview-panel mb-4 mb-xl-0">
                        <h5 class="mb-3">{{ __('permissions.preview.side_title') }}</h5>
                        <p>{{ __('permissions.preview.side_description') }}</p>

                        <div class="permission-preview-note mt-4">
                            <div class="font-weight-bold mb-1">{{ __('permissions.preview.note_title') }}</div>
                            <div class="text-muted">{{ __('permissions.preview.note_body') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="permission-preview-panel">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                            <div>
                                <h5 class="mb-1">{{ __('permissions.preview.tree_title') }}</h5>
                                <p>{{ __('permissions.preview.tree_description') }}</p>
                            </div>
                        </div>

                        <ul id="permissions-preview-tree" class="permission-preview-tree">
                            @foreach ($groupedPermissions as $group)
                                <li>
                                    <button type="button">{{ $group['label'] }}</button>
                                    <div class="permission-group-meta">
                                        {{ trans_choice('permissions.preview.permissions_count', count($group['permissions']), ['count' => count($group['permissions'])]) }}
                                    </div>
                                    <ul>
                                        @foreach ($group['permissions'] as $permission)
                                            <li>
                                                <div class="permission-item">
                                                    <div>
                                                        <strong>{{ $permission->resolved_display_name }}</strong>
                                                        <code>{{ $permission->name }}</code>
                                                    </div>
                                                    <span class="permission-item-badge">
                                                        {{ trans_choice('permissions.preview.roles_count', $permission->roles_count, ['count' => $permission->roles_count]) }}
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/treeview/treeview.js') }}"></script>
    <script>
        $('#permissions-preview-tree').treed({
            openedClass: 'si si-minus',
            closedClass: 'si si-plus'
        });
    </script>
@endsection
