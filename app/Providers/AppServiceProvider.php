<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // تمرير بيانات صورة البروفايل إلى navbar تلقائيًا
        View::composer('partials.navbar.navbar', function ($view) {
            $user = Auth::user();

            $hasCurrentProfile = false;
            $currentProfileImage = null;

            if ($user) {
                $currentProfileImage = $user->images()
                    ->where('type', 'profile')
                    ->where('is_current', true)
                    ->first();

                $hasCurrentProfile = $currentProfileImage !== null;
            }

            $view->with([
                'hasCurrentProfile' => $hasCurrentProfile,
                'currentProfileImage' => $currentProfileImage,
            ]);
        });
    }
}
