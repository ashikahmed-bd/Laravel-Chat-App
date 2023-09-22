<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    // Send message to user
    public function sendMessage(Request $request){

        // validate the request
        $this->validate($request, [
            'user_id' => ['required'],
            'message' => ['required']
        ]);

        $user_id = $request->user_id;
        $user = auth()->user();
        $message = $request->message;

        // check if there is an existing chat
        // between the auth user and the recipient
        $chat = $user->getChatWithUser($user_id);

        if(! $chat){
            $chat = Chat::create([]);
            Chat::createParticipants($chat->id, [$user->id, $user_id]);
        }

        // add the message to the chat
        $message = Message::create([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'message' => $message,
            'last_read_at' => null
        ]);

        return new MessageResource($message);
    }


    // Get chats for user
    public function getUserChats()
    {
        $chats = auth()->user()->chats()
        ->with(['messages', 'participants'])
        ->get();
        return ChatResource::collection($chats);
    }


    // get messages for chat
    public function getChatMessages($id)
    {
        $messages = Message::where('chat_id', $id)->get();
        return MessageResource::collection($messages);
    }

    // mark chat as read
    public function markAsRead($id)
    {
        $chat = Chat::findOrFail($id);
        $chat->markAsReadForUser(auth()->id());
        return response()->json(['message' => 'successful'], 200);
    }

    // destroy message
    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();
    }
}
