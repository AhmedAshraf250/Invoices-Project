@extends('layouts.master2')

@section('title')
    {{ __('auth.titles.login') }} - {{ __('common.app_name') }}
@stop

@section('css')
    <link href="{{ URL::asset('assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css') }}"
        rel="stylesheet">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row no-gutter">
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
                                        <h1 class="main-logo1 ml-1 mr-0 my-auto tx-28">Ahmed<span>-</span>Croco</h1>
                                    </div>

                                    @include('auth.partials.language-switcher')

                                    <div class="main-signup-header">
                                        <h2>{{ __('auth.headings.welcome_back') }}</h2>
                                        <h5 class="font-weight-semibold mb-4">{{ __('auth.headings.sign_in_to_continue') }}
                                        </h5>

                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf

                                            <div class="form-group">
                                                <label for="email">{{ __('auth.fields.email') }}</label>
                                                <input id="email" type="email"
                                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                                    value="{{ old('email') }}" required autocomplete="email" autofocus>
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="password">{{ __('auth.fields.password') }}</label>
                                                <input id="password" type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    name="password" required autocomplete="current-password">
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="form-group form-check mb-3">
                                                <input class="form-check-input" type="checkbox" name="remember"
                                                    id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="remember">
                                                    {{ __('auth.fields.remember') }}
                                                </label>
                                            </div>

                                            <button type="submit" class="btn btn-main-primary btn-block">
                                                {{ __('auth.actions.login') }}
                                            </button>
                                        </form>

                                        <div class="main-signin-footer mt-4">
                                            @if (Route::has('password.request'))
                                                <p><a
                                                        href="{{ route('password.request') }}">{{ __('auth.links.forgot_password') }}</a>
                                                </p>
                                            @endif
                                            @if (Route::has('register'))
                                                <p>{{ __('auth.links.no_account') }}
                                                    <a
                                                        href="{{ route('register') }}">{{ __('auth.actions.create_account') }}</a>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
                <div class="row wd-100p mx-auto text-center">
                    <div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
                        <img src="{{ URL::asset('assets/img/media/login.png') }}"
                            class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection
