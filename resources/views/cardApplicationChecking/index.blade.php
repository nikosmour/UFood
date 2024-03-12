@extends('layouts.nav')
@section('title')
    cardApplicationChecking
@endsection
@section('content')
    <card-application-checking v-bind:items="{{$models}}"/>
@endsection
