@extends('layouts.nav')
@section('title')
    cardApplicant
@endsection
@section('content')
    <div class="container">
        @include('components.cardApplicant.info',['models'=>[$cardApplicant],'caption'=>'cardApplicant'])
    </div>
@endsection
