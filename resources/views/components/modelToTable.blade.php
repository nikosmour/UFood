<div>
    <table>
        <thead>
        <tr>
            @foreach ($models[0]->getAttributes() as $key=> $value)
                <td>{{$key}}</td>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach ($models as $model)
            <tr>
                @foreach ($model->getAttributes() as $value)
                    <td>{{$value}}</td>
                @endforeach
            </tr>
            @if(false==is_null($relations))
                <tr>
                    <td colspan="{{count($model->getAttributes())}}">
                        @foreach($relations as $relation)
                            @include('components.modelToTable',['models'=>(is_countable($model[$relation[0]]))?$model[$relation[0]]: [$model[$relation[0]]],'relations'=>(count($relation)>1)?[array_slice($relation,1)]:[] ])
                        @endforeach
                    </td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
</div>
