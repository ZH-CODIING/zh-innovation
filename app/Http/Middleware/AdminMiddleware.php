<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // التأكد أن المستخدم مسجل دخوله وأنه "أدمن"
        if ($request->user() && $request->user()->role === 'admin') {
            return $next($request);
        }

        // إذا لم يكن أدمن، يتم رفض الوصول
        return response()->json(['message' => 'عذراً، هذه الصلاحية للمديرين فقط!'], 403);
    }
}