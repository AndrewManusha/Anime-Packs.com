<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pack->type . ': ' . $pack->title }}</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
            color: #333;
            padding: 20px;
        }
        .content {
            background: white;
            padding: 20px;
            border-radius: 5px;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>New {{ $pack->type . ': ' . $pack->title }}</h1>
        <p>{!! $pack->description !!}</p>

        <a href="{{ url($pack->page_url) }}" class="button">Open pack</a>

        <p>Thank you for being with us!</p>
        <p>Anime Packs</p>
    </div>
</body>
</html>
