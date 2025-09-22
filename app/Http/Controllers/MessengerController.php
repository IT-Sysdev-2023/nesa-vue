<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessengerController extends Controller
{
    //
    public function getUsers()
    {
        $users = User::all();

        return response()->json([
            'users' => $users
        ]);
    }

    public function  getEveryMessage()
    {
        $users = User::select('users.id as user_id', 'messages.id as id', 'users.firstname', 'users.lastname', 'messages.*')
            ->join('messages', 'messages.sender_id', '=', 'users.id')
            ->where('messages.sender_id', '!=', Auth::user()->id)
            ->selectRaw('MAX(messages.created_at) as last_message_at')
            ->groupBy('users.id')
            ->orderByDesc('last_message_at')
            ->get();
        return response()->json([
            'users' => $users
        ]);
    }

    public function getMessage(Request $request)
    {
        $messages = Message::where('sender_id', $request->user()->id)
            ->where('recipient_id', $request->id)
            ->orWhere('sender_id', $request->id)
            ->orWhere('recipient_id', $request->user()->id)->get();

        return response()->json([
            'rep_id' => $request->id,
            'messages' => $messages
        ]);
    }
    public function sendMessage(Request $request)
    {
        $message = Message::create([
            'sender_id' => $request->user()->id,
            'message' => $request->message,
            'recipient_id' => $request->id,
            'attachment' => '',
            'read' => 0
        ]);

        if($message){
             broadcast(new MessageEvent($request->id,  $message))->toOthers();
        }

        return response()->json([
            'message' => $message
        ]);
    }
}
