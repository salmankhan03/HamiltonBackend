
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
    
    <br />
    <br />
    
    <p>Attachments:-</p>
    
    @foreach ($images as $image)
        <img src={{$image}} widht="20%" height="20%"/>
        <br /><br /><br />
    @endforeach
    

</body>

</html>