@extends('layouts.app')
@section('title')
    entry Checking
@endsection
@section('content')
    <div class="container-fluid my_flex_height">
        <entry-checking
            v-bind:urls="{
                form:'{{route('entryChecking.store')}}',
                statistics: '{{route('home')}}'
            }"
            v-bind:statistics-server="{{$statistics}}"
        >
        </entry-checking>
    </div>
@endsection
