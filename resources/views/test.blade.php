@extends('layouts.app')

@section('content')
    @include('components.modelToTable',compact('models','relations'))
@endsection
