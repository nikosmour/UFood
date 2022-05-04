@extends('layouts.nav')

@section('content')
    <div class="container">
        @if($root_name=='update')
            <button class=" btn btn-danger"
                    onclick="document.getElementById('destroy-form').submit();">
                {{ __('Delete') }}
            </button>
            <form id="destroy-form" class="d-none" action="{{ route('mealPlan.destroy',compact('dailyMealPlan')) }}"
                  method="POST">
                @csrf
                @method('delete')
            </form>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="post" action=
        @if($root_name=='update')
            {{route('mealPlan.update',compact('dailyMealPlan'))}} >
            @method('PUT')
            <div class="form-group">
                <label>{{__("Date")}}
                    <input type="date" class='form-control-plaintext ' readonly
                           value='{{substr($dailyMealPlan->date,0,10)}}'/>
                </label>
            </div>
            @else
                {{route('mealPlan.store')}} >
                <div class="form-group ">
                    <label for="date">{{__("Date")}}</label>
                    <input type="date" id="date" name="date" class='form-control' value="{{old('date')}}"/>
                </div>
            @endif
            @csrf
            @foreach(App\Enum\MealPlanPeriodEnum::names() as $period)
                @foreach($dailyMealPlan[strtolower($period)] as $meal)
                    <div class="form-group">
                        <label
                            for={{$period."[".($loop->index).']'}} >{{__("$period").' -> '.__($meal['category']->name).' -> '.($loop->index+1) }}</label>
                        <input type="text"
                               id={{$period."[".($loop->index).']'}} name={{$period.'['.($loop->index).']'}} class="form-control"
                               value={{old($period,[$loop->index=>$meal['description']] )[$loop->index]}} />
                    </div>
                @endforeach
            @endforeach
            <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
        </form>

    </div>
@endsection
