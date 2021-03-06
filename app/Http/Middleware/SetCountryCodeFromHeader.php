<?php

namespace VotingApp\Http\Middleware;

use Closure;

class SetCountryCodeFromHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $countryCode = $this->getCountryCode($request);

        // Allow overriding country via query string, for debugging
        if ($queryOverride = $request->get('country_code')) {
            $request->session()->put('country_code', $queryOverride);
        }

        if ($request->hasSession() && $request->session()->has('country_code')) {
            $countryCode = $request->session()->get('country_code');
        }

        // Set country code for this request
        config()->set('app.country_code', $countryCode);

        return $next($request);
    }

    /**
     * Get the country code from Fastly's GeoIP Header.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function getCountryCode($request)
    {
        return $request->server('HTTP_X_FASTLY_COUNTRY_CODE', null);
    }
}
