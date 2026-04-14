@extends('layouts.master2')

@section('title')
    {{ __('auth.titles.forgot_password') }} - {{ __('common.app_name') }}
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
                        <img src="{{ URL::asset('assets/img/media/forgot.png') }}"
                            class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-6 col-xl-5 bg-white">
                <div class="login d-flex align-items-center py-2">
                    <div class="container p-0">
                        <div class="row">
                            <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                                <div class="mb-5 d-flex">
                                    <a href="{{ route('home') }}"><img
                                            src="{{ URL::asset('assets/img/brand/favicon.png') }}"
                                            class="sign-favicon ht-40" alt="logo"></a>
                                    <h1 class="main-logo1 ml-1 mr-0 my-auto tx-28">Mora<span>So</span>ft</h1>
                                </div>

                                @include('auth.partials.language-switcher')

                                <div class="main-card-signin d-md-flex bg-white">
                                    <div class="wd-100p">
                                        <div class="main-signin-header">
                                            <h2>{{ __('auth.headings.forgot_password') }}</h2>
                                            <h5 class="mb-3">{{ __('auth.messages.forgot_password_description') }}</h5>

                                            @if (session('status'))
                                                <div class="alert alert-success" role="alert">
                                                    {{ session('status') }}
                                                </div>
                                            @endif

                                            <form method="POST" action="{{ route('password.email') }}">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="email">{{ __('auth.fields.email') }}</label>
                                                    <input id="email" class="form-control @error('email') is-invalid @enderror"
                                                        placeholder="{{ __('auth.placeholders.email') }}" type="email"
                                                        name="email" value="{{ old('email') }}" required autofocus>
                                                    @error('email')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <button class="btn btn-main-primary btn-block" type="submit">
                                                    {{ __('auth.actions.send_reset_link') }}
                                                </button>
                                            </form>
                                        </div>
                                        <div class="main-signup-footer mg-t-20">
                                            <p>{{ __('auth.links.remember_password') }} <a href="{{ route('login') }}">{{ __('auth.actions.login') }}</a></p>
                                        </div>
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
