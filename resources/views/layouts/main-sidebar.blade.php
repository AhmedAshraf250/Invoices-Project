<!-- main-sidebar -->
@php
    $availableGuards = array_keys(config('auth.guards', []));
    $activeGuard =
        collect($availableGuards)->first(fn(string $guard): bool => auth()->guard($guard)->check()) ??
        config('fortify.guard', config('auth.defaults.guard', 'web'));
    $authenticatedUser = auth()->guard($activeGuard)->user();
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
                    <span class="mb-0 text-muted">{{ __('common.user.premium_member') }}</span>
                </div>
            </div>
        </div>
        <ul class="side-menu">
            <li class="side-item side-item-category">{{ __('common.sidebar.app') }}</li>
            <li class="slide">
                <a class="side-menu__item" href="{{ route('page.show', ['page' => 'index']) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3" />
                        <path
                            d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z" />
                    </svg>
                    <span class="side-menu__label">{{ __('common.sidebar.home') }}</span><span
                        class="badge badge-success side-badge">1</span></a>
            </li>
            <li class="side-item side-item-category">{{ __('common.sidebar.invoices') }}</li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                        class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M19 5H5v14h14V5zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" opacity=".3" />
                        <path
                            d="M3 5v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2zm2 0h14v14H5V5zm2 5h2v7H7zm4-3h2v10h-2zm4 6h2v4h-2z" />
                    </svg><span class="side-menu__label">{{ __('common.sidebar.invoices') }}</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item"
                            href="{{ route('invoices.index') }}">{{ __('common.sidebar.invoices_list') }}</a>
                    </li>
                    <li><a class="slide-item"
                            href="{{ route('page.show', ['page' => 'chart-flot']) }}">{{ __('common.sidebar.paid_invoices') }}</a>
                    </li>
                    <li><a class="slide-item"
                            href="{{ route('page.show', ['page' => 'chart-chartjs']) }}">{{ __('common.sidebar.partial_invoices') }}</a>
                    </li>
                    <li><a class="slide-item"
                            href="{{ route('page.show', ['page' => 'chart-echart']) }}">{{ __('common.sidebar.unpaid_invoices') }}</a>
                    </li>

                </ul>
            </li>

            <li class="side-item side-item-category">{{ __('common.sidebar.reports') }}</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                        class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M4 12c0 4.08 3.06 7.44 7 7.93V4.07C7.05 4.56 4 7.92 4 12z" opacity=".3" />
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93s3.05-7.44 7-7.93v15.86zm2-15.86c1.03.13 2 .45 2.87.93H13v-.93zM13 7h5.24c.25.31.48.65.68 1H13V7zm0 3h6.74c.08.33.15.66.19 1H13v-1zm0 9.93V19h2.87c-.87.48-1.84.8-2.87.93zM18.24 17H13v-1h5.92c-.2.35-.43.69-.68 1zm1.5-3H13v-1h6.93c-.04.34-.11.67-.19 1z" />
                    </svg><span class="side-menu__label">{{ __('common.sidebar.reports') }}</span><i
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

            <li class="side-item side-item-category">{{ __('common.sidebar.users') }}</li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                        class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
                        <path
                            d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
                    </svg><span class="side-menu__label">{{ __('common.sidebar.users') }}</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item"
                            href="{{ route('page.show', ['page' => 'mail']) }}">{{ __('common.sidebar.users_list') }}</a>
                    </li>
                    <li><a class="slide-item"
                            href="{{ route('page.show', ['page' => 'mail-compose']) }}">{{ __('common.sidebar.users_permissions') }}</a>
                    </li>
                </ul>
            </li>

            <li class="side-item side-item-category">{{ __('common.sidebar.settings') }}</li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                        class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path d="M6 20h12V10H6v10zm6-7c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z" opacity=".3" />
                        <path
                            d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z" />
                    </svg><span class="side-menu__label">{{ __('common.sidebar.settings') }}</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item"
                            href="{{ route('organizations.index') }}">{{ __('common.sidebar.organizations') }}</a>
                    </li>
                    <li><a class="slide-item"
                            href="{{ route('products.index') }}">{{ __('common.sidebar.products') }}</a>
                    </li>
                </ul>
            </li>

        </ul>
    </div>
</aside>
<!-- main-sidebar -->
