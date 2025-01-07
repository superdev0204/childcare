<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Models\Error;

class Handler extends ExceptionHandler
{
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }
    
    /**
     * Register the exception handling callbacks for the application.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return parent::render($request, $exception);
        }
        else{
            // Check if the URL contains 'apple-touch-icon'
            if (strpos($request->fullUrl(), 'apple-touch-icon') !== false || strpos($request->fullUrl(), '.php') !== false) {
                // Do not store in the error table
                return response()->view('errors.index', compact('exception'));
            }

            // if ($exception instanceof NotFoundHttpException) {
            //     // Custom logic for handling 404 errors
            //     return response()->view('errors.404', compact('exception'), 404);
            // }
            else{
                // if user from the share internet   
                if(!empty($_SERVER['HTTP_CLIENT_IP'])) {   
                    $client_ip = $_SERVER['HTTP_CLIENT_IP'];
                }   
                //if user is from the proxy   
                elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   
                    $client_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                }   
                //if user is from the remote address   
                else{   
                    $client_ip = $_SERVER['REMOTE_ADDR'];
                }

                // Ensure the IP address does not exceed 45 characters
                if (strlen($client_ip) > 20) {
                    $client_ip = substr($client_ip, 0, 20);
                }

                // Sanitize user agent string
                $user_agent = preg_replace('/[^\x20-\x7E]/', '', $request->header('User-Agent')); // Remove non-ASCII characters
                
                // Store error data in the error table
                $error = [
                    'url' => $request->fullUrl(),
                    'host' => $request->getHost(),
                    'errortype' => get_class($exception),
                    'ip' => $client_ip,
                    'user_agent' => $user_agent,
                    // 'date' => date('Y-m-d h:i:s'),
                    'referrer' => $request->header('referer') ? ((strlen($request->header('referer')) > 255) ? substr($request->header('referer'), 0, 255) : $request->header('referer')) : "",
                    'exception' => (strlen($exception->getMessage()) > 65000) ? json_encode(substr($exception->getMessage(), 0, 65000), JSON_UNESCAPED_UNICODE) : json_encode($exception->getMessage(), JSON_UNESCAPED_UNICODE)
                ];
                Error::create($error);

                return response()->view('errors.index', compact('exception'));
            }
        }
    }
}
