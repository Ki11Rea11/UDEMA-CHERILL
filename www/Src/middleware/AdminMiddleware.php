<?php

namespace Src\middleware;

use Closure;
use Laminas\Diactoros\Response\RedirectResponse;
use ORM;
use Psr\Http\Message\ServerRequestInterface;

class AdminMiddleware
{
    public function handle(ServerRequestInterface $request, Closure $next)
    {
        $admin = ORM::forTable('users')->find_one($_SESSION['user_id']);
        if ($admin->is_admin == 1){
            return $next($request);
        }
        return new RedirectResponse('/');

    }

}