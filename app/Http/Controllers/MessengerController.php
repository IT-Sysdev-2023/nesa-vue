<?php

namespace App\Http\Controllers;

use App\Events\GetEveryMessageEvent;
use App\Events\MessageEvent;
use App\Events\SeenMessageEvent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
            'users.photo',
            'users.firstname',
            'users.lastname',
            'users.id as user_id',
            DB::raw('MAX(GREATEST(COALESCE(m1.id, 0), COALESCE(m2.id, 0))) as last_message_id'),
            DB::raw('MAX(GREATEST(COALESCE(m1.created_at, "1970-01-01"), COALESCE(m2.created_at, "1970-01-01"))) as last_message_time')
        )
            ->when(isset($request->search) && $request->search != '', function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('firstname', 'like', '%' . $request->search . '%')
                        ->orWhere('lastname', 'like', '%' . $request->search . '%');
                });
            })
            ->leftJoin('messages as m1', function ($join) use ($currentUser) {
                $join->on('m1.sender_id', '=', 'users.id')
                    ->where('m1.recipient_id', '=', $currentUser->id);
            })
            ->leftJoin('messages as m2', function ($join) use ($currentUser) {
                $join->on('m2.recipient_id', '=', 'users.id')
                    ->where('m2.sender_id', '=', $currentUser->id);
            })
            ->groupBy('users.username', 'users.firstname', 'users.lastname', 'users.id', 'users.photo')
            ->having('last_message_id', '>', 0) // Add this line to filter users with messages
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

        $unreadCountsRe = Message::where('read', 0)
            ->whereIn('recipient_id',  $userIds)
            ->where('sender_id', $currentUser->id)
            ->selectRaw('recipient_id, COUNT(*) as count')
            ->groupBy('recipient_id')
            ->pluck('count', 'recipient_id');

        $seenPhoto = Message::join('users', 'users.id', '=', 'messages.sender_id')
            ->whereIn('sender_id', $userIds)
            ->select('sender_id', 'users.photo')
            ->groupBy('sender_id', 'users.photo')
            ->pluck('users.photo', 'sender_id');


        $totalUnread = $unreadCounts->sum();

        // 🔹 Attach latest message + unread count
        $users->transform(function ($user) use ($messageMap, $unreadCounts, $currentUser, $unreadCountsRe, $seenPhoto) {


            $messages = $messageMap->get($user->user_id, collect());
            // dd($messages);

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
            $user->countUnread = $unreadCountsRe->get($user->user_id, 0);

            $user->photoSeen =  $seenPhoto->get($user->user_id, 0);

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
        })->orderBy('id')->get();
        $messages->transform(function ($item) {
            $item->toReply = Message::where('id', $item->reply)->value('message');
            return $item;
        });

        return response()->json([
            'rep_id' => $request->id,
            'messages' => $messages,
            'name' => User::select('firstname', 'lastname')->where('id', $request->id)->value('fullName'),
            'photo' => User::where('id', $request->id)->value('photo'),
            'isOffline' => User::where('id', $request->id)->value('is_online'),
        ]);
    }
    private function executionPhpIni()
    {
        ini_set('upload_max_filesize', '50M');
        ini_set('post_max_size', '50M');

        return $this;
    }
    public function sendMessage(Request $request)
    {


        $filename = '';

        if ($request->hasFile('attachment')) {

            $file = $request->file('attachment'); // single

            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename, 'public');
        }

        $content = match (true) {
            isset($request->message) => $request->message,
            $request->hasFile('attachment') => 'sent a photo',
            default => null
        };

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'message' => $content,
            'recipient_id' => $request->id,
            'attachment' => $filename,
            'reply' => isset($request->replyId) ? $request->replyId : 0,
            'read' => 0
        ]);


        if ($message) {

            $message->toReply = Message::where('id', $message->reply)->value('message');

            broadcast(new MessageEvent($request->id,  $message))->toOthers();
        }

        return response()->json([
            'message' => $message
        ]);
    }

    public function seenMessage(Request $request)
    {

        $messages = Message::where([
            ['sender_id', $request->id],
            ['recipient_id', $request->user()->id],
            ['read', 0],
        ])->get();

        if ($messages->isNotEmpty()) {
            // Attach reply message text


            // Mark them all as read
            $updated = Message::whereIn('id', $messages->pluck('id'))->update(['read' => 1]);

            if ($updated) {
                $lastMessage = $messages->sortByDesc('id')->first();
                $lastMessage->refresh(); // 👈 reloads fresh values from DB

                foreach ($messages as $msg) {
                    $msg->toReply = Message::where('id', $msg->reply)->value('message');
                }

                broadcast(new SeenMessageEvent($request->id, $lastMessage))->toOthers();
            }
        }


        return response()->json([
            'message' => $lastMessage
        ]);
    }
    public function reactMessage(Request $request)
    {
        $updated = Message::where('id', $request->id)->update(['react' => $request->react]);

        if ($updated) {
            $message = Message::where('id', $request->id)->first();
            if ($message->reply != 0) {
                $message->toReply = Message::where('id', $message->reply)->value('message');
            }
            broadcast(new MessageEvent($request->rep,  $message))->toOthers();
        }

        return response()->json([
            'message' => $message
        ]);
    }

    public function expandeMessage()
    {
        return inertia('Message/MessageIndexExpanded', []);
    }
}
