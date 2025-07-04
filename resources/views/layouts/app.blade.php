<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Social App')</title>
    <link rel="stylesheet" href="{{ mix('css/style.css') }}">
    <link rel="stylesheet" href="{{ mix('/css/bootstrap.min.css') }}">
    @auth
        <link rel="stylesheet" href="{{ mix('css/nav-auth.css') }}">
    @else
        <link rel="stylesheet" href="{{ mix('css/navbar.css') }}">
    @endauth
    {{-- <link href="{{ mix('css/app.css') }}" rel="stylesheet"> --}}

     @yield('styles') 
</head>
<body>

    @include('partials.navbar.navbar')
    
    <main >
        @yield('content')
    </main>
    
    <script src="{{ mix('js/script.js') }}"></script>
    <script src="{{ mix('/js/bootstrap.min.js') }}"></script>

</body>
</html>