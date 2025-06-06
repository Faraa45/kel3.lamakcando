<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CostumerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // return $next($request);
        // Cek apakah user sudah login dan memiliki user_group = 'costumer'
        if (auth()->check() && auth()->user()->user_group === 'costumer') {
            return $next($request); // Lanjut ke request berikutnya
        }

        // Jika bukan admin, redirect ke halaman lain atau tampilkan error
        return abort(403, 'Anda tidak memiliki akses');
    }
}