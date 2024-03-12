@extends('layouts.nav')
@section('title')
    entry Checking
@endsection
@section('content')
    <div class="container-fluid my_flex_height">
        <entry-checking
            v-bind:statistics-server="{{$statistics}}"
        >
        </entry-checking>
    </div>
@endsection
