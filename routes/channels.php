<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('plan-invite.{invitedUserId}', function ($user, $invitedUserId) {
    return (int) $user->id === (int)$invitedUserId;
});

Broadcast::channel('reject-invite.{inviterUserId}', function ($user, $inviterUserId) {
    return (int) $user->id === (int)$inviterUserId;
});

Broadcast::channel('accept-invite.{inviterUserId}', function ($user, $inviterUserId) {
    return (int) $user->id === (int)$inviterUserId;
});
