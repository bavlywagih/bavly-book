<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class InjectUserImages
{
    // public function handle($request, Closure $next)
    // {
    //     $user = Auth::user();

    //     if ($user) {
    //         foreach (['cover', 'profile'] as $type) {
    //             $hasCurrent = $user->images()->where('type', $type)->where('is_current', true)->exists();
    //             if (!$hasCurrent) {
    //                 $latest = $user->images()->where('type', $type)->latest()->first();
    //                 if ($latest) {
    //                     $latest->is_current = true;
    //                     $latest->save();
    //                 }
    //             }
    //         }

    //         $profilePhoto = $user->images()->where('type', 'profile')->where('is_current', true)->first();
    //         $coverPhoto = $user->images()->where('type', 'cover')->where('is_current', true)->first();

    //         View::share('navbarUser', $user);
    //         View::share('navbarProfilePhoto', $profilePhoto);
    //         View::share('navbarCoverPhoto', $coverPhoto);
    //     }

    //     return $next($request);
    // }
}
