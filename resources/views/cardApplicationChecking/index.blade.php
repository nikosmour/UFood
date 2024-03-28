@extends('layouts.nav')
@section('title')
    {{  $category->value. ' Card Applications' }}
@endsection
@section('content')
    <card-application-checking v-bind:items="{{$models}}" v-bind:category="'{{$category->value}}'"/>
@endsection
