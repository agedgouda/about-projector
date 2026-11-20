<?php

use Illuminate\Support\Facades\Route;

// Route::statamic('example', 'example-view', [
//    'title' => 'Example'
// ]);

Route::statamic('blog', 'blog/index', [
    'title' => 'Blog',
]);

// Unlinked landing page so "projector login" searches find an indexed result; the app's real login is projecthq.app/login.
Route::statamic('login', 'login', [
    'title' => 'Log In',
    'meta_description' => 'Log in to Projector (projecthq.app), the project management system for tasks, meetings, and team workflows.',
]);

Route::get('sitemap.xml', \App\Http\Controllers\SitemapController::class);

// The demo lives in the app; send visitors straight there.
Route::redirect('demo', 'https://projecthq.app/demo');
