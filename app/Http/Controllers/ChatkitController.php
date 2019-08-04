<?php

namespace App\Http\Controllers;

use Chatkit\Chatkit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Chatkit\Exceptions\ChatkitException;
use App\Room;

class ChatkitController extends Controller
{
    public function __construct(Chatkit $chatkit)
    {
        $this->chatkit = $chatkit;
    }

    public function authenticate(Request $request)
    {
        $userId = $request->get('user_id');

        $response = $this->chatkit->authenticate(['user_id' => $userId]);

        return response()->json($response['body'], $response['status']);
    }

    public function newCustomer(Request $request)
    {
        $data = $request->validate(['name' => 'required|string', 'email' => 'required|email']);

        $data = [
            'name' => $data['name'],
            'id' => Str::slug(Str::lower($data['email'])),
        ];

        try {
            $this->createRoomForUser(
                $user = $this->chatkit->createUser($data)['body']
            );
        } catch (ChatkitException $e) {
            $user = $e->getBody()['error'] === 'services/chatkit/user_already_exists'
                ? $data
                : $e->getBody();
        }

        return response()->json($user, $user ? 200 : 403);
    }

    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'userId' => 'required',
            'text' => 'required|string',
            'roomId' => 'required|string',
        ]);

        // Send the customers message to chatkit
        $this->chatkit->sendSimpleMessage([
            'sender_id' => $data['userId'],
            'room_id' => $data['roomId'],
            'text' => $data['text'],
        ]);

        $room = Room::findOrFail($data['roomId']);

        if ($room->handled_by_bot) {
            $user = $this->chatkit->getUser(['id' => $data['userId']])['body'];
            $query = collect(app('wit')->getIntentByText($data['text'])['entities']);

            $this->chatkit->sendSimpleMessage([
                'sender_id' => 'admin',
                'room_id' => $data['roomId'],
                'text' => app('wit')->handleCustomerQuery($query, $user, $room->id),
            ]);
        }

        return response()->json(['success' => true]);
    }

    protected function createRoomForUser(array $user)
    {
        $roomId = Str::random();

        $response = $this->chatkit->createRoom([
            'id' => $roomId,
            'creator_id' => $user['id'],
            'name' => $user['name'],
            'user_ids' => ['admin'],
            'private' => true,
        ]);

        Room::create(['id' => $roomId, 'customer_id' => $user['id']]);

        $this->chatkit->sendSimpleMessage([
            'sender_id' => 'admin',
            'room_id' => $roomId,
            'text' => 'Hello ' . $user['name'] . ', how may I help you?'
        ]);

        return $response;
    }
}
