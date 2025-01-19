@php use App\Enum\MealPlanPeriodEnum; @endphp
        <!DOCTYPE html>
<html>
<head>
    <meta charset = "UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">
    <title>Card Application Status Updated</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #4CAF50;
            margin: 0;
        }

        .content {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
        }

        .cta {
            display: inline-block;
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class = "container" lang = 'el'>
    <!-- Greek Content -->
    <div class = "header">
        <h1>Μεταβολή υπολοίπου</h1>
    </div>
    <div class = "content">
        <p>Αγαπητέ Χρήστη,</p>
        <p>Μια νέα συναλλαγή ({{__('transactions.'.$latestTransaction->transaction,[],'el')}}) έχει τροποποιήσει το
           υπόλοιπο κουπονιών σας στις
            <b>{{ $model->updated_at}}</b>. Αριθμός συναλλαγής : {{$latestTransaction->id}}
        </p>
        <table>
            <thead>
            <tr>
                <th>Γεύματα</th>
                <th>Παλιό Υπόλοιπο</th>
                <th>Τιμή Συναλλαγής</th>
                <th>Νέο Υπόλοιπο</th>
            </tr>
            </thead>
            <tbody>
            @foreach(MealPlanPeriodEnum::names() as $period)
                <tr>
                    <th class = "text-center">{{__('meals.'.$period, [],'el')}}</th>
                    <td class = "text-center">{{$model[$period]-$latestTransaction[$period]}}</td>
                    <td class = "text-center">{{$latestTransaction[$period]}}</td>
                    <td class = "text-center">{{$model[$period]}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class = "text-center">
            <a href = "{{ url('/coupon/history') }}" class = "cta">Δείτε τις κινήσεις σας</a>
        </div>
    </div>
</div>
    <div class = "container" lang = 'en'>
        <!-- English Content -->
        <div class = "header">
            <h1>Balance Updated</h1>
        </div>
        <div class = "content">
            <p>Dear User,</p>
            <p>A new transaction ({{__('transactions.'.$latestTransaction->transaction,[],'en')}}) has modified your
               coupon
               balance at
                <b>{{ $model->updated_at}}</b>. Transaction Id : {{$latestTransaction->id}}
            </p>
            <table>
                <thead>
                <tr>
                    <th>Meals</th>
                    <th>Old Balance</th>
                    <th>Transaction Value</th>
                    <th>New Balance</th>
                </tr>
                </thead>
                <tbody>

                @foreach(MealPlanPeriodEnum::names() as $period)
                    @if($latestTransaction->transaction !== 'using' || $latestTransaction[$period] !== 0)
                        <tr>
                            <th>{{__('meals.'.$period,[],'en')}}</th>
                            <td class = "text-center">{{$model[$period]-$latestTransaction[$period]}}</td>
                            <td class = "text-center">{{$latestTransaction[$period]}}</td>
                            <td class = "text-center">{{$model[$period]}}</td>
                        </tr>
                    @endif
                @endforeach

                </tbody>
            </table>

            <div class = "text-center">
                <a href = "{{ url('/coupon/history') }}" class = "cta">View your moves</a>
            </div>
        </div>
    </div>
    <div class = "footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </div>
</body>
</html>
