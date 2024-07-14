<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $data = $request->validate([
            'text' => 'required|string',
        ]);

        $message = ChatMessage::create($data);

        $messages = ChatMessage::latest('created_at')->get();
        $messages = $messages->map(function ($item) {
            return [
                'text' => $item->text,
                'created_at' => $item->created_at->diffForHumans(),
            ];
        });

        broadcast(new MessageSent($messages));

        return response()->json([
            'success' => true,
            'message' => 'Message sent!',
            'data' => $message,
        ]);
    }
}
