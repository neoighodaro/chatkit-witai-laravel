@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row" id="mainrow">
        <nav class="col-sm-3 col-md-2 d-none d-sm-block bg-light sidebar">
            <h5 class="container">Available Rooms</h5>
            <ul class="nav nav-pills flex-column" id="rooms">
            </ul>
        </nav>
        <main role="main" class="col-sm-9 ml-sm-auto col-md-10 pt-3" id="main">
            <h1>Chats</h1>
            <p>👈 Select a chat to load the messages</p>
            <p>&nbsp;</p>
            <div class="chat" style="margin-bottom:150px">
                <h5 id="room-title"></h5>
                <p>&nbsp;</p>
                <div class="response">
                    <form id="replyMessage">
                        <div class="form-group">
                            <input type="text" placeholder="Enter Message" class="form-control" name="message" />
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <tbody id="chat-msgs">
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

@endsection
