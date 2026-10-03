<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Statamic\Events\NavTreeSaved;
use App\Bard\TextColor;
use Statamic\Fieldtypes\Bard\Augmentor;
use Statamic\Statamic;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Statamic::vite('app', [
            'input' => [
                'resources/js/cp.js',
                'resources/css/cp.css',
            ],
            'hotFile' => public_path('cp-hot'),
            'buildDirectory' => 'vendor/app',
        ]);

        Augmentor::addExtension('textColor', new TextColor);

        Event::listen(NavTreeSaved::class, function () {
            \Statamic\Facades\Stache::clear();
        });
    }
}
