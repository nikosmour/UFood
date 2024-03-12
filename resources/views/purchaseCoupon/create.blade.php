@extends('layouts.nav')
@section('title')
    purchase coupons
@endsection
@section('content')
    <div class="container-fluid my_flex_height">
        <purchase-coupon v-bind:statistics-server="{{$statistics}}"/>
    </div>
@endsection
