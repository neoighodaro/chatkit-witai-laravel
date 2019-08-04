<?php

namespace App\Http\Controllers;

use Chatkit\Chatkit;
use Illuminate\Http\Request;

class ChatkitController extends Controller
{
    public function __construct(Chatkit $chatkit)
    {
        $this->middleware('auth');

        $this->chatkit = $chatkit;
    }

    public function authenticate(Request $request)
    {
        $userId = $request->get('user_id');

        $response = $this->chatkit->authenticate(['user_id' => $userId]);

        return response()->json($response['body'], $response['status']);
    }
}
