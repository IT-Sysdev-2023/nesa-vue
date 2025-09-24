<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
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

        $currentUser = $request->user();

        // 🔹 Get users with last message timestamp
        $users = User::select(
            'users.username',
            'users.firstname',
            'users.lastname',
            'users.id as user_id',
            DB::raw('MAX(GREATEST(COALESCE(m1.id, 0), COALESCE(m2.id, 0))) as last_message_id'),
            DB::raw('MAX(GREATEST(COALESCE(m1.created_at, "1970-01-01"), COALESCE(m2.created_at, "1970-01-01"))) as last_message_time')
        )
            ->leftJoin('messages as m1', function ($join) use ($currentUser) {
                $join->on('m1.sender_id', '=', 'users.id')
                    ->where('m1.recipient_id', '=', $currentUser->id);
            })
            ->leftJoin('messages as m2', function ($join) use ($currentUser) {
                $join->on('m2.recipient_id', '=', 'users.id')
                    ->where('m2.sender_id', '=', $currentUser->id);
            })
            ->groupBy('users.username', 'users.firstname', 'users.lastname', 'users.id')
            ->orderBy('last_message_time', 'desc')
            ->get();

        $userIds = $users->pluck('user_id')->all();

        // 🔹 Fetch messages between current user and others
        $messages = Message::where(function ($query) use ($userIds, $currentUser) {
            $query->whereIn('sender_id', $userIds)
                ->where('recipient_id', $currentUser->id);
        })
            ->orWhere(function ($query) use ($userIds, $currentUser) {
                $query->where('sender_id', $currentUser->id)
                    ->whereIn('recipient_id', $userIds);
            })
            ->orderByDesc('id')
            ->get();

        // 🔹 Group messages by the "other user"
        $messageMap = $messages->groupBy(function ($message) use ($currentUser) {
            return $message->sender_id == $currentUser->id
                ? $message->recipient_id
                : $message->sender_id;
        });

        // 🔹 Count unread messages from others
        $unreadCounts = Message::where('read', 0)
            ->whereIn('sender_id', $userIds)
            ->where('recipient_id', $currentUser->id)
            ->selectRaw('sender_id, COUNT(*) as count')
            ->groupBy('sender_id')
            ->pluck('count', 'sender_id');

        $totalUnread = $unreadCounts->sum();

        // 🔹 Attach latest message + unread count
        $users->transform(function ($user) use ($messageMap, $unreadCounts) {
            $messages = $messageMap->get($user->user_id, collect());
            $latestMessage = $messages->first(); // already ordered DESC

            $user->latest_at = $latestMessage?->created_at
                ? Date::parse($latestMessage->created_at)->format('h:i A')
                : null;
            $user->count = $unreadCounts->get($user->user_id, 0);

            if ($unreadCounts->get($user->user_id, 0) >= 2) {
                $getMessage = '+' . $unreadCounts->get($user->user_id, 0) . ' messages';
            } else {
                $getMessage = isset($latestMessage->message) ?  $latestMessage?->message : 'no message';
            }

            $user->message =  $getMessage;

            return $user;
        });

        return response()->json([
            'users' => $users,
            'total_unread' => $totalUnread,
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
            'messages' => $messages,
            'name' => User::select('firstname','lastname')->where('id', $request->id)->value('fullName'),
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

    public function seenMessage(Request $request)
    {
        return  Message::where([
            ['sender_id', $request->id],
            ['read', 0],
        ])->update([
            'read' => 1
        ]);
    }
}
