<?php

namespace App\Broadcasting;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class OnlineUsers
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user)
    {
        User::where('id', $user->id)->update([
            'is_online' => now()
        ]);

        return [
            'id' => $user->id,
            'name' => $user->full_name,
            'aname' => strtolower($user->lastname . ', ' . $user->firstname),
            'photo' => $user->photo,
            'time' => now(),
        ];
    }
}
