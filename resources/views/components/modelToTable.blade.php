<div>
    <table class="table text-center  table-hover table-col-to-row-sm">
        <thead  class="thead-dark">
            <tr>
                @foreach ($models[0]->getAttributes() as $key=> $value)
                    <th scope="col">{{$key}}</th>
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
