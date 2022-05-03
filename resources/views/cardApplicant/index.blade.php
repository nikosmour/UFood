@extends('layouts.nav')
@section('title')
        cardApplicant
@endsection
@section('content')
    <div class="container">
        @include('components.cardApplicant.info',['models'=>[$cardApplicant],'relations'=>[['usageCard']],'caption'=>'cardApplicant'])
    </div>

@endsection
