<?php

use Chatkit\Chatkit;
use Illuminate\Http\Request;

// Creates a new room
Route::get('/room', function (Chatkit $chatkit, Request $request) {
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
    ]);

    // If there is a room with the email address then return the ID for the room
    // Create the new room in chatkit and save the ID to the database as a session
});
