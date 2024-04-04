<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$caption}}</title>
    <style>
        /* Your CSS styles here */
    </style>
</head>
<body>
@include('components.modelToTable',compact('models','caption'))

</body>
</html>
