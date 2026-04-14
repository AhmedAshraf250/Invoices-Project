@extends('layouts.master2')

@section('title')
    {{ __('auth.titles.verify_email') }} - {{ __('common.app_name') }}
@stop

@section('css')
    <link href="{{ URL::asset('assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row no-gutter">
            <div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
                <div class="row wd-100p mx-auto text-center">
                    <div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
                        <img src="{{ URL::asset('assets/img/media/login.png') }}"
                            class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-6 col-xl-5 bg-white">
                <div class="login d-flex align-items-center py-2">
                    <div class="container p-0">
                        <div class="row">
                            <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                                <div class="card-sigin">
                                    <div class="mb-5 d-flex">
                                        <a href="{{ route('home') }}"><img
                                                src="{{ URL::asset('assets/img/brand/favicon.png') }}"
                                                class="sign-favicon ht-40" alt="logo"></a>
                                        <h1 class="main-logo1 ml-1 mr-0 my-auto tx-28">Mora<span>So</span>ft</h1>
                                    </div>

                                    @include('auth.partials.language-switcher')

                                    <div class="main-signup-header">
                                        <h2>{{ __('auth.headings.verify_email') }}</h2>
                                        <h5 class="font-weight-semibold mb-4">{{ __('auth.messages.verify_email_description') }}</h5>

                                        @if (session('status') === 'verification-link-sent')
                                            <div class="alert alert-success" role="alert">
                                                {{ __('auth.messages.verification_sent') }}
                                            </div>
                                        @endif

                                        @if (Route::has('verification.send'))
                                            <form method="POST" action="{{ route('verification.send') }}">
                                                @csrf
                                                <button type="submit" class="btn btn-main-primary btn-block">
                                                    {{ __('auth.actions.resend_verification') }}
                                                </button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('logout') }}" class="mt-3">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary btn-block">
                                                {{ __('auth.actions.logout') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
