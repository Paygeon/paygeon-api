<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

         // Check if the API key header is present
         if(!$request->header('client-id'))
         {
            return response()->json(['error' => 'Unauthorized. Client ID  is missing.'], 401);
         }
         else if (!$request->header('key')) {
            return response()->json(['error' => 'Unauthorized. API key is missing.'], 401);
         }
         else if(!$request->header('secret-key'))
         {
            return response()->json(['error' => 'Unauthorized. SECRET KEY is missing.'], 401);
         }
         else
         {
            $user = User::with("CheckbookRotatekey")
            ->where("checkbook_id", $request->header("client-id"))
            ->whereHas("CheckbookRotatekey", function ($q) use ($request) {
                $q->where('key', $request->header('key'))
                ->where("secret",$request->header('secret-key'));
            });
            if(!$user->exists())
            {
                return response()->json(['error' => 'Unauthorized. Given Authoization was wrong'], 401);
            }
            else
            {
                return $next($request);
            }
         }

    }
}
