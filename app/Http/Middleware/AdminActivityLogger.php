<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AdminActivityLogger
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $user = $request->user();

            if ($user && $user->is_admin && $request->is('admin*')) {
                DB::table('admin_activity_logs')->insert([
                    'user_id' => $user->id,
                    'action' => $request->route()?->getName(),
                    'method' => $request->getMethod(),
                    'path' => $request->path(),
                    'ip_address' => $request->ip(),
                    'user_agent' => (string) $request->userAgent(),
                    'meta' => json_encode([
                        'query' => $request->query(),
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // no-op
        }

        return $response;
    }
}
