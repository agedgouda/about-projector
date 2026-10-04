<?php

use Illuminate\Support\Facades\Route;

// Route::statamic('example', 'example-view', [
//    'title' => 'Example'
// ]);

Route::statamic('blog', 'blog/index', [
    'title' => 'Blog',
]);

Route::get('sitemap.xml', \App\Http\Controllers\SitemapController::class);
