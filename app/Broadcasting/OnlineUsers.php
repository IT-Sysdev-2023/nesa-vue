<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\UserType;
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

        $role = UserType::where('id', $user->usertype)->first();

        return [
            'id' => $user->id,
            'name' => $user->full_name,
            'aname' => strtolower($user->lastname . ', ' . $user->firstname),
            'photo' => $user->photo,
            'role' => $role->name,
            'time' => now(),
        ];
    }
}
