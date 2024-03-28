@php use App\Enum\CardStatusEnum; @endphp
@extends('layouts.nav')

@section('content')
    <div class="container">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            <p>Your Application status is {{$cardApplication->cardLastUpdate->status}}</p>
            <card-application-edit-form
                {{--            v-bind:url="'{{route('cardApplication.update',$cardApplication)}}'"--}}
                {{--                                    v-bind:url-doc="'{{route('document.store',$cardApplication)}}'",--}}
                v-bind:card-application="{{$cardApplication}}"
                {{--                                    v-bind:doc-files="{{$files}}"--}}
                v-bind:application-edit='{{in_array($cardApplication->cardLastUpdate->status,[CardStatusEnum::TEMPORARY_SAVED, CardStatusEnum::INCOMPLETE, CardStatusEnum::SUBMITTED])}}'
        >
        </card-application-edit-form>
    </div>
@endsection
