<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function getEveryMessage(Request $request)
    {

        $users = User::select(
            'users.username',
            'users.firstname',
            'users.lastname',
            'users.id as user_id',
            'm2.message',
            DB::raw('MAX(m2.read) as read_unread'),
            DB::raw('MAX(GREATEST(COALESCE(m1.id, 0), COALESCE(m2.id, 0))) as last_message_id'),
            DB::raw('MAX(GREATEST(COALESCE(m1.created_at, "1970-01-01"), COALESCE(m2.created_at, "1970-01-01"))) as last_message_time')
        )
            ->leftJoin('messages as m1', 'm1.sender_id', '=', 'users.id')
            ->leftJoin('messages as m2', 'm2.recipient_id', '=', 'users.id')
            ->groupBy(
                'users.username',
                'users.firstname',
                'users.lastname',
                'users.id',
            )
            // 👉 sort ASC by last message timestamp
            ->orderBy('last_message_time', 'desc')
            ->get();

        // $userIds = $users->pluck('id')->all();


        return response()->json([
            'users' => $users
        ]);
    }

    public function getMessage(Request $request)
    {
        $messages = Message::where(function ($q) use ($request) {
            $q->where('sender_id', $request->user()->id)
                ->where('recipient_id', $request->id);
        })->orWhere(function ($q) use ($request) {
            $q->where('sender_id', $request->id)
                ->where('recipient_id', $request->user()->id);
        })->get();

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

        if ($message) {
            broadcast(new MessageEvent($request->id,  $message))->toOthers();
        }

        return response()->json([
            'message' => $message
        ]);
    }
}
