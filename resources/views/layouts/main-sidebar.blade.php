<!-- main-sidebar -->
@php
    $availableGuards = array_keys(config('auth.guards', []));
    $activeGuard =
        collect($availableGuards)->first(fn(string $guard): bool => auth()->guard($guard)->check()) ??
        config('fortify.guard', config('auth.defaults.guard', 'web'));
    $authenticatedUser = auth()->guard($activeGuard)->user();
    $isUsersSectionActive =
        request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*');
    $isCustomersSectionActive = request()->routeIs('organizations.*') || request()->routeIs('products.*');
    $isSettingsSectionActive = request()->routeIs('page.show')
        && in_array(request()->route('page'), ['profile', 'notification', 'mail-settings'], true);
@endphp
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ route('page.show', ['page' => 'home']) }}"><img
                src="{{ URL::asset('assets/img/brand/logo.png') }}" class="main-logo" alt="logo"></a>

    </div>
    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="user-img" class="avatar avatar-xl brround"
                        src="{{ URL::asset('assets/img/faces/6.jpg') }}"><span
                        class="avatar-status profile-status bg-green"></span>
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">
                        {{ $authenticatedUser?->name ?? __('common.user.guest') }}</h4>
                    <span class="mb-0 text-muted">{{ $authenticatedUser->email ?? '' }}</span>
                    {{-- <span class="mb-0 text-muted">{{ __('common.user.premium_member') }}</span> --}}
                </div>
            </div>
        </div>
        <ul class="side-menu">
            <li class="side-item side-item-category">{{ __('common.sidebar.app') }}</li>
            <li class="slide">
                <a class="side-menu__item" href="{{ route('page.show', ['page' => 'index']) }}">
                    <i class="side-menu__icon fe fe-home"></i>
                    <span class="side-menu__label">{{ __('common.sidebar.home') }}</span><span
                        class="badge badge-success side-badge">1</span></a>
            </li>
            <li class="side-item side-item-category">{{ __('common.sidebar.invoices') }}</li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#"><i
                        class="side-menu__icon fe fe-file-text"></i><span
                        class="side-menu__label">{{ __('common.sidebar.invoices') }}</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li>
                        <a class="slide-item {{ request()->routeIs('invoices.index') ? 'active' : '' }}"
                            href="{{ route('invoices.index') }}">{{ __('common.sidebar.invoices_list') }}</a>
                    </li>
                    <li>
                        <a class="slide-item {{ request()->routeIs('invoices.show') ? 'active' : '' }}"
                            href="{{ request()->routeIs('invoices.show') ? url()->current() : '#' }}">{{ __('common.sidebar.invoice_details') }}</a>
                    </li>
                    <li>
                        <a class="slide-item {{ request()->routeIs('invoices.status') && request()->route('status') === 'paid' ? 'active' : '' }}"
                            href="{{ route('invoices.status', ['status' => 'paid']) }}">{{ __('common.sidebar.paid_invoices') }}</a>
                    </li>
                    <li>
                        <a class="slide-item {{ request()->routeIs('invoices.status') && request()->route('status') === 'partial' ? 'active' : '' }}"
                            href="{{ route('invoices.status', ['status' => 'partial']) }}">{{ __('common.sidebar.partial_invoices') }}</a>
                    </li>
                    <li>
                        <a class="slide-item {{ request()->routeIs('invoices.status') && request()->route('status') === 'unpaid' ? 'active' : '' }}"
                            href="{{ route('invoices.status', ['status' => 'unpaid']) }}">{{ __('common.sidebar.unpaid_invoices') }}</a>
                    </li>
                    <li>
                        <a class="slide-item {{ request()->routeIs('invoices.archived') ? 'active' : '' }}"
                            href="{{ route('invoices.archived') }}">{{ __('common.sidebar.archived_invoices') }}</a>
                    </li>
                </ul>
            </li>

            <li class="side-item side-item-category">{{ __('common.sidebar.reports') }}</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#"><i
                        class="side-menu__icon fe fe-pie-chart"></i><span
                        class="side-menu__label">{{ __('common.sidebar.reports') }}</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item"
                            href="{{ route('page.show', ['page' => 'cards']) }}">{{ __('common.sidebar.invoice_reports') }}</a>
                    </li>
                    <li><a class="slide-item"
                            href="{{ route('page.show', ['page' => 'darggablecards']) }}">{{ __('common.sidebar.customer_reports') }}</a>
                    </li>
                </ul>
            </li>

            @canany(['users.view', 'roles.view', 'permissions.view'])
                <li class="side-item side-item-category">{{ __('common.sidebar.users') }}</li>
                <li @class(['slide', 'active is-expanded' => $isUsersSectionActive])>
                    <a @class(['side-menu__item', 'active' => $isUsersSectionActive]) data-toggle="slide" href="#"><i
                            class="side-menu__icon fe fe-users"></i><span
                            class="side-menu__label">{{ __('common.sidebar.users') }}</span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu" @style(['display: block' => $isUsersSectionActive])>
                        @can('users.view')
                            <li><a class="slide-item {{ request()->routeIs('users.*') ? 'active' : '' }}"
                                    href="{{ route('users.index') }}">{{ __('common.sidebar.users_list') }}</a></li>
                        @endcan
                        @can('roles.view')
                            <li><a class="slide-item {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                                    href="{{ route('roles.index') }}">{{ __('common.sidebar.users_roles') }}</a></li>
                        @endcan
                        @can('permissions.view')
                            <li><a class="slide-item {{ request()->routeIs('permissions.*') ? 'active' : '' }}"
                                    href="{{ route('permissions.index') }}">{{ __('common.sidebar.users_permissions') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            @canany(['organizations.view', 'products.view'])
                <li class="side-item side-item-category">{{ __('common.sidebar.customers') }}</li>

                <li @class(['slide', 'active is-expanded' => $isCustomersSectionActive])>
                    <a @class(['side-menu__item', 'active' => $isCustomersSectionActive]) data-toggle="slide" href="#"><i
                            class="side-menu__icon mdi mdi-account-multiple"></i><span
                            class="side-menu__label">{{ __('common.sidebar.customers') }}</span><i
                            class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu" @style(['display: block' => $isCustomersSectionActive])>
                        @can('organizations.view')
                            <li><a class="slide-item {{ request()->routeIs('organizations.*') ? 'active' : '' }}"
                                    href="{{ route('organizations.index') }}">{{ __('common.sidebar.organizations') }}</a>
                            </li>
                        @endcan
                        @can('products.view')
                            <li><a class="slide-item {{ request()->routeIs('products.*') ? 'active' : '' }}"
                                    href="{{ route('products.index') }}">{{ __('common.sidebar.products') }}</a></li>
                        @endcan
                    </ul>
                </li>
            @endcanany

            <li class="side-item side-item-category">{{ __('common.sidebar.settings') }}</li>
            <li @class(['slide', 'active is-expanded' => $isSettingsSectionActive])>
                <a @class(['side-menu__item', 'active' => $isSettingsSectionActive]) data-toggle="slide" href="#"><i
                        class="side-menu__icon fe fe-settings"></i><span
                        class="side-menu__label">{{ __('common.sidebar.settings') }}</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu" @style(['display: block' => $isSettingsSectionActive])>
                    <li><a class="slide-item {{ request()->routeIs('page.show') && request()->route('page') === 'profile' ? 'active' : '' }}"
                            href="{{ route('page.show', ['page' => 'profile']) }}">{{ __('common.sidebar.profile') }}</a>
                    </li>
                    <li><a class="slide-item {{ request()->routeIs('page.show') && request()->route('page') === 'notification' ? 'active' : '' }}"
                            href="{{ route('page.show', ['page' => 'notification']) }}">{{ __('common.sidebar.notifications') }}</a>
                    </li>
                    <li><a class="slide-item {{ request()->routeIs('page.show') && request()->route('page') === 'mail-settings' ? 'active' : '' }}"
                            href="{{ route('page.show', ['page' => 'mail-settings']) }}">{{ __('common.sidebar.mail_settings') }}</a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</aside>
<!-- main-sidebar -->
