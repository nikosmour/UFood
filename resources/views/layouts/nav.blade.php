@extends('layouts.app')
@section('title')
    {{ config('app.name', 'Laravel') }}
@endsection
{{--@section('nav_list')
    @auth
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ __('Buying Coupons')  }} <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{route('coupons.buying.index')}}">
                    {{ __('Buying Coupons') }}
                </a>
            </div>
        </li>
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ __('student.nav.Free Food')  }} <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="#">
                    {{ __('student.nav.History') }}
                </a>
                <a class="dropdown-item" href="#">
                    {{ __('student.nav.Request') }}
                </a>
            </div>
        </li>

        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ __('student.nav.Coupons')  }} <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{route('coupons.history')}}">
                    {{ __('student.nav.History') }}
                </a>
                <a class="dropdown-item" href="{{route('coupons.send.create')}}">
                    {{ __('student.nav.New Transaction') }}
                </a>
            </div>
        </li>
    @endauth
    <li class="nav-item {{ Route::is('programFood.*') ? 'active' : '' }}">
        <a class="nav-link " href="{{ route('programFood.index')}}">{{ __('student.nav.Program Food') }}</a>
    </li>
    <li class="nav-item {{ Route::is('contact.*') ? 'active' : '' }}">
        <a class="nav-link" href="https://www.upatras.gr/el/node/5585" target="_blank">{{ __('student.nav.Contact') }}</a>
    </li>


@endsection--}}

