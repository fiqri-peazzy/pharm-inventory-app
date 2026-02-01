<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $action = match($request->method()) {
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => 'unknown'
            };

            $module = $this->getModuleFromRoute($request->route()?->getName());

            if ($module) {
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'module' => $module,
                    'record_id' => $request->route('id'),
                    'old_values' => null,
                    'new_values' => json_encode($request->except(['_token', 'password', 'password_confirmation'])),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return $response;
    }

    private function getModuleFromRoute(?string $routeName): ?string
    {
        if (!$routeName) return null;

        $parts = explode('.', $routeName);
        return $parts[0] ?? null;
    }
}