@extends('layouts.nav')

@section('content')
    <div class="container">
        @include('components.couponOwner.balance')
        {{($temp=$couponOwner) ?'' : ''}}
        <div >
            <table class="table text-center  table-hover table-col-to-row-sm" >
                <caption>{{ __('student.nav.Coupons') }}</caption>
                <thead  class="thead-dark">
                <tr>
                    <th scope="col" >{{__('Category')}}</th>
                    <th scope="col" >{{__('Comments')}}</th>
                    <th scope="col" >{{__('Money')}}</th>
                    <th scope="col" >{{__('Breakfast')}}</th>
                    <th scope="col" >{{__('Lunch')}}</th>
                    <th scope="col" >{{__('Dinner')}}</th>
                    <th scope="col" >{{__('Date')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <th scope="row" >
                            {{__($transaction->transaction)}}
                        </th>
                        <td>{{ $transaction->money ? : ''}}
                            @foreach(\App\Enum\MealPlanPeriodEnum::names() as $meal)
                                {{__($meal)}}: {{$transaction[$meal]}}
                            @endforeach
                            @if($transaction->transaction=='receiving')
                                {{__('Sender').': '.$transaction->academic_id}}</td>
                                <td>{{ $temp->money}}{{($temp->money-=$transaction->money)?'':'' }} € </td>
                                @foreach(\App\Enum\MealPlanPeriodEnum::names() as $meal)
                                    <td>{{$temp[$meal]}}{{($temp[$meal]-=$transaction[$meal])?'':''}}</td>
                                @endforeach
                            @elseif($transaction->transaction=='sending')
                                {{__('Receiver').': '.$transaction->academic_id}}</td>
                                <td>{{ $temp->money}}{{($temp->money+=$transaction->money)?'':'' }} € </td>
                                @foreach(\App\Enum\MealPlanPeriodEnum::names() as $meal)
                                    <td>{{$temp[$meal]}}{{($temp[$meal]+=$transaction[$meal])?'':''}}</td>
                                @endforeach
                            @elseif($transaction->transaction=='buying')
                                </td>
                                <td>{{ $temp->money}}{{($temp->money+=$transaction->money)?'':'' }} € </td>
                                @foreach(\App\Enum\MealPlanPeriodEnum::names() as $meal)
                                    <td>{{$temp[$meal]}}{{($temp[$meal]-=$transaction[$meal])?'':''}}</td>
                                @endforeach
                            @endif
                        <td>{{$transaction->created_at}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
