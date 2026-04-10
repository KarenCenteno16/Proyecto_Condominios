<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



// use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Mail;

// Route::get('/test-mail', function () {
//     Mail::raw('Si lees esto, el SMTP de Google funciona perfectamente.', function ($message) {
//         $message->to('tu_correo@gmail.com')->subject('Prueba de Sistema');
//     });
//     return "Correo enviado correctamente";
// });