<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Broadcast::channel('chat.{id}', function ($user, $id) {
//     // El admin puede entrar a cualquier chat, el residente solo al suyo
//     return $user->id === (int) $id || $user->rol === 'admin';
// });
