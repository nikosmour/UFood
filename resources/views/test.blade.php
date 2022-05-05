@extends('layouts.nav')
@section('title')
    {{ $caption}}
@endsection

@section('content')
    <div class="container">
        @include('components.modelToTable',compact('models','caption'))
    </div>
@endsection
