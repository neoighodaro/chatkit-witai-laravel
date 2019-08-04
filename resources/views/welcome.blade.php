<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/guest.css') }}">
        <script src="{{ asset('js/guest.js') }}"></script>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">Alfred</div>
            </div>
        </div>

        <div class="chatbubble">
            <div class="unexpanded">
                <span>Chat with Support</span>
            </div>
            <div class="expanded chat-window">
                <div class="login-screen container">

                <form id="loginScreenForm">
                    <div class="form-group">
                    <input type="text" class="form-control" id="fullname" placeholder="Name*" required>
                    </div>
                    <div class="form-group">
                    <input type="email" class="form-control" id="email" placeholder="Email Address*" required>
                    </div>
                    <button type="submit" class="btn btn-block btn-primary">Start Chat</button>
                </form>

                </div>
                <div class="chats">
                <div class="loader-wrapper">
                    <div class="loader">
                    <span>{</span><span>}</span>
                    </div>
                </div>
                <ul class="messages clearfix">
                </ul>
                <div class="input">
                    <form class="form-inline" id="messageSupport">
                    <div class="form-group">
                        <input type="text" autocomplete="off" class="form-control" id="newMessage" placeholder="Enter Message">
                    </div>
                    <button type="submit" class="btn btn-primary">Send</button>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </body>
</html>
