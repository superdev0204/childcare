<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Visitors;

class VisitorCounterMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $page_url = request()->url();
        $current_date = date('Y-m-d');
        
        if(env('VISITOR_TRACK') == 'on' && (strpos($page_url, 'visitor_counts') === false && strpos($page_url, 'visitor_delete') === false)){
            $exists = Visitors::where('page_url', $page_url)->where('date', $current_date)->exists();
            if ($exists) {
                Visitors::where('page_url', $page_url)->where('date', $current_date)->increment('visitor_count');
            } else {
                Visitors::create([
                    'page_url' => $page_url,
                    'date' => $current_date,
                    'visitor_count' => 1
                ]);
            }
        }
        
        return $next($request);
    }
}
