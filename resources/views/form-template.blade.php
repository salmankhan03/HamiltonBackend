<!DOCTYPE html>

<html>

<head>

    <title>Form Response</title>

</head>

<body>

    <h1>{{ $formType }}</h1>

    <p><u>Form Response</u></p>

    @foreach ($data as $key => $value)
        <b>{{ $key }} :-</b> <p>{{ $value }}</p>
    @endforeach

</body>

</html>