<!-- main-header opened -->
@php
    $availableGuards = array_keys(config('auth.guards', []));
    $activeGuard =
        collect($availableGuards)->first(fn(string $guard): bool => auth()->guard($guard)->check()) ??
        config('fortify.guard', config('auth.defaults.guard', 'web'));
    $authenticatedUser = auth()->guard($activeGuard)->user();
    $currentLocale = app()->getLocale();
    $currentLanguageFlag = $currentLocale === 'ar' ? 'assets/img/flags/EG.png' : 'assets/img/flags/us_flag.jpg';

    $fortifyGuard = config('fortify.guard', config('auth.defaults.guard', 'web'));
    $logoutRouteName =
        $activeGuard === $fortifyGuard || !Route::has($activeGuard . '.logout') ? 'logout' : $activeGuard . '.logout';
@endphp
<div class="main-header sticky side-header nav nav-item">
    <div class="container-fluid">
        <div class="main-header-left ">
            <div class="responsive-logo">
                <a href="{{ route('home') }}"><img src="{{ URL::asset('assets/img/brand/logo.png') }}" class="logo-1"
                        alt="logo"></a>
                <a href="{{ route('home') }}"><img src="{{ URL::asset('assets/img/brand/logo-white.png') }}"
                        class="dark-logo-1" alt="logo"></a>
                <a href="{{ route('home') }}"><img src="{{ URL::asset('assets/img/brand/favicon.png') }}" class="logo-2"
                        alt="logo"></a>
                <a href="{{ route('home') }}"><img src="{{ URL::asset('assets/img/brand/favicon.png') }}"
                        class="dark-logo-2" alt="logo"></a>
            </div>
            <div class="app-sidebar__toggle" data-toggle="sidebar">
                <a class="open-toggle" href="#"><i class="header-icon fe fe-align-left"></i></a>
                <a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
            </div>
            <div class="main-header-center mr-3 d-sm-none d-md-none d-lg-block">
                <input class="form-control" placeholder="{{ __('common.header.search_anything') }}" type="search">
                <button class="btn"><i class="fas fa-search d-none d-md-block"></i></button>
            </div>
        </div>
        <div class="main-header-right">
            <ul class="nav">
                <li class="">
                    <div class="dropdown  nav-itemd-none d-md-flex">
                        <a href="#" class="d-flex  nav-item nav-link pl-0 country-flag1" data-toggle="dropdown"
                            aria-expanded="false">
                            <span class="avatar country-Flag mr-0 align-self-center bg-transparent"><img
                                    src="{{ URL::asset($currentLanguageFlag) }}" alt="img"></span>
                            <div class="my-auto">
                                <strong
                                    class="mr-2 ml-2 my-auto">{{ app()->isLocale('ar') ? __('common.language.arabic') : __('common.language.english') }}</strong>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-left dropdown-menu-arrow" x-placement="bottom-end">
                            <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('en', null, [], true) }}"
                                class="dropdown-item d-flex">
                                <span class="avatar ml-3 align-self-center bg-transparent"><img
                                        src="{{ URL::asset('assets/img/flags/us_flag.jpg') }}" alt="img"></span>
                                <div class="d-flex">
                                    <span class="mt-2">{{ __('common.language.english') }}</span>
                                </div>
                            </a>
                            <a href="{{ \Mcamara\LaravelLocalization\Facades\LaravelLocalization::getLocalizedURL('ar', null, [], true) }}"
                                class="dropdown-item d-flex">
                                <span class="avatar ml-3 align-self-center bg-transparent"><img
                                        src="{{ URL::asset('assets/img/flags/EG.png') }}" alt="img"></span>
                                <div class="d-flex">
                                    <span class="mt-2">{{ __('common.language.arabic') }}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="nav nav-item  navbar-nav-right ml-auto">
                <div class="nav-link" id="bs-example-navbar-collapse-1">
                    <form class="navbar-form" role="search">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="{{ __('common.header.search') }}">
                            <span class="input-group-btn">
                                <button type="reset" class="btn btn-default">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button type="submit" class="btn btn-default nav-link resp-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-search">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
                <div class="dropdown nav-item main-header-message ">
                    <a class="new nav-link" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                            class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-mail">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                            </path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg><span class=" pulse-danger"></span></a>
                    <div class="dropdown-menu">
                        <div class="menu-header-content bg-primary text-right">
                            <div class="d-flex">
                                <h6 class="dropdown-title mb-1 tx-15 text-white font-weight-semibold">
                                    {{ __('common.header.messages') }}</h6>
                                <span
                                    class="badge badge-pill badge-warning mr-auto my-auto float-left">{{ __('common.header.mark_all_read') }}</span>
                            </div>
                            <p class="dropdown-title-text subtext mb-0 text-white op-6 pb-0 tx-12 ">
                                {{ __('common.header.unread_messages', ['count' => 4]) }}
                            </p>
                        </div>
                        <div class="main-message-list chat-scroll">
                            <a href="#" class="p-3 d-flex border-bottom">
                                <div class="  drop-img  cover-image  "
                                    data-image-src="{{ URL::asset('assets/img/faces/3.jpg') }}">
                                    <span class="avatar-status bg-teal"></span>
                                </div>
                                <div class="wd-90p">
                                    <div class="d-flex">
                                        <h5 class="mb-1 name">Petey Cruiser</h5>
                                    </div>
                                    <p class="mb-0 desc">{{ __('common.header.message_preview_1') }}</p>
                                    <p class="time mb-0 text-left float-right mr-2 mt-2">Mar 15 3:55 PM</p>
                                </div>
                            </a>
                            <a href="#" class="p-3 d-flex border-bottom">
                                <div class="drop-img cover-image"
                                    data-image-src="{{ URL::asset('assets/img/faces/2.jpg') }}">
                                    <span class="avatar-status bg-teal"></span>
                                </div>
                                <div class="wd-90p">
                                    <div class="d-flex">
                                        <h5 class="mb-1 name">Jimmy Changa</h5>
                                    </div>
                                    <p class="mb-0 desc">{{ __('common.header.message_preview_2') }}</p>
                                    <p class="time mb-0 text-left float-right mr-2 mt-2">Mar 06 01:12 AM</p>
                                </div>
                            </a>
                            <a href="#" class="p-3 d-flex border-bottom">
                                <div class="drop-img cover-image"
                                    data-image-src="{{ URL::asset('assets/img/faces/9.jpg') }}">
                                    <span class="avatar-status bg-teal"></span>
                                </div>
                                <div class="wd-90p">
                                    <div class="d-flex">
                                        <h5 class="mb-1 name">Graham Cracker</h5>
                                    </div>
                                    <p class="mb-0 desc">{{ __('common.header.message_preview_3') }}</p>
                                    <p class="time mb-0 text-left float-right mr-2 mt-2">Feb 25 10:35 AM</p>
                                </div>
                            </a>
                            <a href="#" class="p-3 d-flex border-bottom">
                                <div class="drop-img cover-image"
                                    data-image-src="{{ URL::asset('assets/img/faces/12.jpg') }}">
                                    <span class="avatar-status bg-teal"></span>
                                </div>
                                <div class="wd-90p">
                                    <div class="d-flex">
                                        <h5 class="mb-1 name">Donatella Nobatti</h5>
                                    </div>
                                    <p class="mb-0 desc">{{ __('common.header.message_preview_4') }}</p>
                                    <p class="time mb-0 text-left float-right mr-2 mt-2">Feb 12 05:12 PM</p>
                                </div>
                            </a>
                            <a href="#" class="p-3 d-flex border-bottom">
                                <div class="drop-img cover-image"
                                    data-image-src="{{ URL::asset('assets/img/faces/5.jpg') }}">
                                    <span class="avatar-status bg-teal"></span>
                                </div>
                                <div class="wd-90p">
                                    <div class="d-flex">
                                        <h5 class="mb-1 name">Anne Fibbiyon</h5>
                                    </div>
                                    <p class="mb-0 desc">{{ __('common.header.message_preview_5') }}</p>
                                    <p class="time mb-0 text-left float-right mr-2 mt-2">Jan 29 03:16 PM</p>
                                </div>
                            </a>
                        </div>
                        <div class="text-center dropdown-footer">
                            <a href="#">{{ __('common.header.view_all') }}</a>
                        </div>
                    </div>
                </div>
                <div class="dropdown nav-item main-header-notification">
                    <a class="new nav-link" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-bell">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg><span class=" pulse"></span></a>
                    <div class="dropdown-menu">
                        <div class="menu-header-content bg-primary text-right">
                            <div class="d-flex">
                                <h6 class="dropdown-title mb-1 tx-15 text-white font-weight-semibold">
                                    {{ __('common.header.notifications') }}
                                </h6>
                                <span
                                    class="badge badge-pill badge-warning mr-auto my-auto float-left">{{ __('common.header.mark_all_read') }}</span>
                            </div>
                            <p class="dropdown-title-text subtext mb-0 text-white op-6 pb-0 tx-12 ">
                                {{ __('common.header.unread_notifications', ['count' => 4]) }}
                            </p>
                        </div>
                        <div class="main-notification-list Notification-scroll">
                            <a class="d-flex p-3 border-bottom" href="#">
                                <div class="notifyimg bg-pink">
                                    <i class="la la-file-alt text-white"></i>
                                </div>
                                <div class="mr-3">
                                    <h5 class="notification-label mb-1">
                                        {{ __('common.header.notification_new_files') }}</h5>
                                    <div class="notification-subtext">{{ __('common.header.time_10_hours_ago') }}
                                    </div>
                                </div>
                                <div class="mr-auto">
                                    <i class="las la-angle-left text-left text-muted"></i>
                                </div>
                            </a>
                            <a class="d-flex p-3" href="#">
                                <div class="notifyimg bg-purple">
                                    <i class="la la-gem text-white"></i>
                                </div>
                                <div class="mr-3">
                                    <h5 class="notification-label mb-1">
                                        {{ __('common.header.notification_updates_available') }}</h5>
                                    <div class="notification-subtext">{{ __('common.header.time_2_days_ago') }}</div>
                                </div>
                                <div class="mr-auto">
                                    <i class="las la-angle-left text-left text-muted"></i>
                                </div>
                            </a>
                            <a class="d-flex p-3 border-bottom" href="#">
                                <div class="notifyimg bg-success">
                                    <i class="la la-shopping-basket text-white"></i>
                                </div>
                                <div class="mr-3">
                                    <h5 class="notification-label mb-1">
                                        {{ __('common.header.notification_new_order') }}</h5>
                                    <div class="notification-subtext">{{ __('common.header.time_1_hour_ago') }}</div>
                                </div>
                                <div class="mr-auto">
                                    <i class="las la-angle-left text-left text-muted"></i>
                                </div>
                            </a>
                            <a class="d-flex p-3 border-bottom" href="#">
                                <div class="notifyimg bg-warning">
                                    <i class="la la-envelope-open text-white"></i>
                                </div>
                                <div class="mr-3">
                                    <h5 class="notification-label mb-1">
                                        {{ __('common.header.notification_new_review') }}</h5>
                                    <div class="notification-subtext">{{ __('common.header.time_1_day_ago') }}</div>
                                </div>
                                <div class="mr-auto">
                                    <i class="las la-angle-left text-left text-muted"></i>
                                </div>
                            </a>
                            <a class="d-flex p-3 border-bottom" href="#">
                                <div class="notifyimg bg-danger">
                                    <i class="la la-user-check text-white"></i>
                                </div>
                                <div class="mr-3">
                                    <h5 class="notification-label mb-1">
                                        {{ __('common.header.notification_verified_registrations') }}</h5>
                                    <div class="notification-subtext">{{ __('common.header.time_2_hours_ago') }}</div>
                                </div>
                                <div class="mr-auto">
                                    <i class="las la-angle-left text-left text-muted"></i>
                                </div>
                            </a>
                            <a class="d-flex p-3 border-bottom" href="#">
                                <div class="notifyimg bg-primary">
                                    <i class="la la-check-circle text-white"></i>
                                </div>
                                <div class="mr-3">
                                    <h5 class="notification-label mb-1">
                                        {{ __('common.header.notification_project_approved') }}</h5>
                                    <div class="notification-subtext">{{ __('common.header.time_4_hours_ago') }}</div>
                                </div>
                                <div class="mr-auto">
                                    <i class="las la-angle-left text-left text-muted"></i>
                                </div>
                            </a>
                        </div>
                        <div class="dropdown-footer">
                            <a href="">{{ __('common.header.view_all') }}</a>
                        </div>
                    </div>
                </div>
                <div class="nav-item full-screen fullscreen-button">
                    <a class="new nav-link full-screen-link" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                            class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-maximize">
                            <path
                                d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3">
                            </path>
                        </svg></a>
                </div>
                <div class="dropdown main-profile-menu nav nav-item nav-link">
                    <a class="profile-user d-flex" href=""><img alt=""
                            src="{{ URL::asset('assets/img/faces/6.jpg') }}"></a>
                    <div class="dropdown-menu">
                        <div class="main-header-profile bg-primary p-3">
                            <div class="d-flex wd-100p">
                                <div class="main-img-user"><img alt=""
                                        src="{{ URL::asset('assets/img/faces/6.jpg') }}" class=""></div>
                                <div class="mr-3 my-auto">
                                    <h6>{{ $authenticatedUser?->name ?? __('common.user.guest') }}</h6>
                                    <span>{{ __('common.user.premium_member') }}</span>
                                </div>
                            </div>
                        </div>
                        <a class="dropdown-item" href=""><i
                                class="bx bx-user-circle"></i>{{ __('common.header.profile') }}</a>
                        <a class="dropdown-item" href=""><i class="bx bx-cog"></i>
                            {{ __('common.header.edit_profile') }}</a>
                        <a class="dropdown-item" href=""><i
                                class="bx bxs-inbox"></i>{{ __('common.header.inbox') }}</a>
                        <a class="dropdown-item" href=""><i
                                class="bx bx-envelope"></i>{{ __('common.header.messages') }}</a>
                        <a class="dropdown-item" href=""><i class="bx bx-slider-alt"></i>
                            {{ __('common.header.account_settings') }}</a>
                        @if (Route::has($logoutRouteName))
                            <form method="POST" action="{{ route($logoutRouteName) }}">
                                @csrf
                                @if ($logoutRouteName !== 'logout')
                                    <input type="hidden" name="guard" value="{{ $activeGuard }}">
                                @endif
                                <button type="submit" class="dropdown-item border-0 bg-transparent w-100 text-right">
                                    <i class="bx bx-log-out"></i> {{ __('auth.actions.logout') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="dropdown main-header-message right-toggle">
                    <a class="nav-link pr-0" data-toggle="sidebar-left" data-target=".sidebar-left">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-menu">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /main-header -->
