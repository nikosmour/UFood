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
        <h1>Αλλαγή της κατάστασης της αίτησης σας</h1>
    </div>
    <div class = "content">
        <p>Αγαπητέ χρήστη,</p>
        <p>Σας ενημερώνουμε ότι η κατάσταση της αίτησής σας για κάρτα έχει ενημερωθεί σε
            <strong>{{ __('status.' . $model->status->name, [], 'el') }}</strong>.
        </p>
        <div class = "text-center">
            <a href = "{{ url('/card/application') }}" class = "cta">Δείτε την Αίτηση</a>
        </div>
    </div>
</div>

<div class = "container" lang = 'en'>
    <!-- English Content -->
    <div class = "header">
        <h1>Card Application Status Updated</h1>
    </div>
    <div class = "content">
        <p>Dear User,</p>
        <p>We are notifying you that the status of your card application has been updated to
            <strong>{{ __('status.' . $model->status->name, [], 'en') }}</strong>.
        </p>
        <div class = "text-center">
            <a href = "{{ url('/card/application') }}" class = "cta">View your Application</a>
        </div>
    </div>
</div>
<div class = "footer">
    <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
</div>
</body>
</html>
