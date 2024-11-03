<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcast::routes(['middleware' => ['auth:api']]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Broadcast::channel('data-updated.{userId}', function (int $userId, string $category) {
//     return true;
// });
