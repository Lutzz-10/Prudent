<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;


class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
{
    App::setLocale('id');
    \Carbon\Carbon::setLocale('id');
}
}
