@extends('layouts.nav')

@section('content')
    <div class="container">
        @include('components.cardApplicant.info',['models'=>[$cardApplicant],'relations'=>[['usageCard']]])
    </div>

@endsection
