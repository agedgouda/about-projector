<?php

use Illuminate\Support\Facades\Route;

// Route::statamic('example', 'example-view', [
//    'title' => 'Example'
// ]);

Route::statamic('blog', 'blog/index', [
    'title' => 'Blog',
]);

Route::get('sitemap.xml', \App\Http\Controllers\SitemapController::class);

// The demo lives in the app; send visitors straight there.
Route::redirect('demo', 'https://projecthq.app/demo');
