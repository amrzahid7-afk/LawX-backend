<?php

namespace App\Http\Middleware;

use App\Enums\RoleSlug;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlatformAccess
{
    private const CLIENT_ALLOWED_PLATFORMS = ['mobile'];
    private const SYNDICATE_ALLOWED_PLATFORMS = ['web'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $platform = $request->header('X-App-Platform');

        if (! $platform) {
            return response()->json(['message' => 'رأس X-App-Platform مطلوب.'], 400);
        }

        if (
            $user->role?->slug === RoleSlug::Client->value
            && ! in_array($platform, self::CLIENT_ALLOWED_PLATFORMS, true)
        ) {
            return response()->json(['message' => 'this access will be from mobile App only '], 403);
        }

        if (
            $user->role?->slug === RoleSlug::Syndicate->value
            && ! in_array($platform, self::SYNDICATE_ALLOWED_PLATFORMS, true)
        ) {
            return response()->json(['message' => 'this access will be from web App  only '], 403);
        }
        return $next($request);
    }
}
