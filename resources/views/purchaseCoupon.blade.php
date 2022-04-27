@extends('layouts.app')

@section('content')
    <div class="container-fluid my_flex_height">
        <purchase-coupon
            v-bind:urls="{
                form:'{{route('coupons.purchase.store')}}',
                statistics: '{{route('home')}}'
            }"
            v-bind:statistics-server="{{$statistics}}"
        >
        </purchase-coupon>
    </div>
@endsection
