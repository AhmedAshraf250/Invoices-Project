@extends('layouts.master2')

@section('title')
    {{ __('auth.titles.register') }} - {{ __('common.app_name') }}
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
                                        <h2>{{ __('auth.headings.create_account') }}</h2>
                                        <h5 class="font-weight-semibold mb-4">{{ __('auth.headings.register_subtitle') }}</h5>

                                        <form method="POST" action="{{ route('register') }}">
                                            @csrf

                                            <div class="form-group">
                                                <label for="name">{{ __('auth.fields.name') }}</label>
                                                <input id="name" type="text"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    name="name" value="{{ old('name') }}" required autofocus
                                                    autocomplete="name">
                                                @error('name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="email">{{ __('auth.fields.email') }}</label>
                                                <input id="email" type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    name="email" value="{{ old('email') }}" required autocomplete="email">
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
                                                    name="password" required autocomplete="new-password">
                                                @error('password')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="password_confirmation">{{ __('auth.fields.password_confirmation') }}</label>
                                                <input id="password_confirmation" type="password" class="form-control"
                                                    name="password_confirmation" required autocomplete="new-password">
                                            </div>

                                            <button type="submit" class="btn btn-main-primary btn-block">
                                                {{ __('auth.actions.create_account') }}
                                            </button>
                                        </form>

                                        <div class="main-signup-footer mt-4">
                                            <p>{{ __('auth.links.already_have_account') }}
                                                <a href="{{ route('login') }}">{{ __('auth.actions.login') }}</a>
                                            </p>
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
