<?php

namespace Appstract\RefererRedirector\Middleware;

use Closure;
use Carbon\Carbon;
use Appstract\RefererRedirector\RefererRedirect;

class RedirectReferer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($referer = $request->headers->get('referer')) {
            $referer = $this->parseReferer($referer);

            // check if referer is in the database and if in current time period
            $redirect = RefererRedirect::where('referer_url', $referer)
                ->where('start_date', '<', Carbon::now())
                ->where(function ($query) {
                    $query->where('end_date', '>', Carbon::now())
                          ->orWhere('end_date', null);
                })
                ->first();

            if (! is_null($redirect)) {
                return redirect()->to('//'.$redirect->redirect_url, 302, $request->headers->all());
            }
        }

        return $next($request);
    }

    /**
     * Parse referer.
     *
     * @param  string $referer
     * @return string
     */
    protected function parseReferer($referer)
    {
        return str_replace(
            ['http://', 'https://'],
            '',
            ends_with($referer, '/') ? mb_substr($referer, 0, -1) : $referer
        );
    }
}
