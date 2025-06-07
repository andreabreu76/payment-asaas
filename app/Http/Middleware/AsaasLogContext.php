<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AsaasLogContext
{
    public function handle(Request $request, Closure $next)
    {
        // Gerar um request_id único se não existir
        $requestId = $request->header('X-Request-ID') ?? (string) Str::uuid();

        // Adicionar o request_id ao header da resposta
        $response = $next($request);
        $response->header('X-Request-ID', $requestId);

        // Configurar o contexto do log
        Log::withContext([
            'request_id' => $requestId,
            'user_id' => auth()->id() ?? 'guest',
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $response;
    }
}
