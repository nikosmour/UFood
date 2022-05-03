@extends('layouts.nav')
@section('title')
    coupon transfer
@endsection
@section('content')
    <div class="container">
        @include('components.couponOwner.balance')
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="post" action={{route('coupons.transfer.store')}} >
            @csrf
            <div class="form-group">
                <label for="receiver_id">{{__("Receiver id")}}</label>
                <input type="number" id='receiver_id' name='receiver_id' class="form-control" min="1" required/>
            </div>
            @foreach(\App\Enum\MealPlanPeriodEnum::names() as $meal )
                <div class="form-group">
                    <label for={{$meal}}>{{__($meal)}}</label>
                    <input type="number" id={{$meal}} name={{$meal}} class="form-control" value="0" min="0"
                           max={{$couponOwner[$meal]}} required/>
                </div>
            @endforeach
            <button type="submit" class="btn btn-primary">{{__('Send')}}</button>
        </form>

    </div>
@endsection
