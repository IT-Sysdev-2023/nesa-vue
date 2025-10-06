<?php

use App\Broadcasting\OnlineUsers;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('syncing-products.{id}', fn(User $user, $id) => (int) $user->id === (int) $id);
Broadcast::channel('message.{id}', fn(User $user, $id) => (int) $user->id === (int) $id);
Broadcast::channel('message-count.{id}', fn(User $user, $id) => (int) $user->id === (int) $id);
Broadcast::channel('message-seen.{id}', fn(User $user, $id) => (int) $user->id === (int) $id);
Broadcast::channel('get-message.{id}', fn(User $user, $id) => (int) $user->id === (int) $id);
Broadcast::channel('online.users', OnlineUsers::class);
