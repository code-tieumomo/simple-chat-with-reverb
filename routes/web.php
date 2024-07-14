<?php

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Route;

Route::get('', function () {
    $messages = ChatMessage::latest('created_at')->get();
    $messages = $messages->map(function ($item) {
        return [
            'text' => $item->text,
            'created_at' => $item->created_at->diffForHumans(),
        ];
    });

    return view('pages.index', compact('messages'));
})->name('index');
