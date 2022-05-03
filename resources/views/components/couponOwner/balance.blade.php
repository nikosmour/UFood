<div>
    <table class="table text-center  table-hover table-col-to-row-sm caption-top">
        <caption>{{ __('balance') }}</caption>
        <thead class="thead-dark">
        <tr>
            <th scope="col"> &nbsp</th> {{-- to do same size --}}
            <th scope="col">{{__('Money')}}</th>
            <th scope="col">{{__('Breakfast')}}</th>
            <th scope="col">{{__('Lunch')}}</th>
            <th scope="col">{{__('Dinner')}}</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th scope="row">{{__('Balance')}} </th>
            <td>{{$couponOwner->money}} €</td>
            @foreach(\App\Enum\MealPlanPeriodEnum::names() as $meal )
                <td>{{$couponOwner[$meal]}}</td>
            @endforeach
        </tr>
        </tbody>
    </table>
</div>

{{--<div class='table-responsive d-sm-none'>
    <table class="table text-center  table-hover" >
        <caption>{{ __('student.nav.Coupons') }}</caption>
        <thead  class="thead-dark">
            <tr>
                <th scope="col " ></th>
                <th scope="row" >{{__('Balance')}} </th>
            </tr>
        </thead>
        <tbody>
        <tr>
            <th scope="col" >{{__('Money')}}</th>
            <td>{{$couponOwner->money}} € </td>
        </tr>
        <tr>
            <th scope="col" >{{__('Breakfast')}}</th>
            <td>{{$couponOwner->breakfast}}</td>
        </tr>
        <tr>
            <th scope="col" >{{__('Lunch')}}</th>
            <td>{{$couponOwner->lunch}}</td>
        </tr>
        <tr>
            <th scope="col" >{{__('Dinner')}}</th>
            <td>{{$couponOwner->dinner}}</td>
        </tr>
        </tbody>
    </table>
</div>--}}
