<?php

/**
 * Author: Marc Newton <code@marcnewton.co.uk>
 * Link: https://gist.github.com/marcnewton/87d530845d9626f5e02412414e7900a2
 *
 * Auto set Laravel's locale to user preferred locale.
 *
 * Detects a list of a users locale preferences in order of preference from their web browser and finds
 * the highest language preference available on the server by scanning the "resources/lang" directories.
 *
 * Covers users that speak multiple languages and language variants with their order of preference.
 *
 * Checks User model has an authenticated user with a saved `locale` value: `Auth::user()->locale`, this value is set as first preference before web browser preferences.
 *
 * Defaults to config "app.locale" else "en" if no preference match can be found.
 *
 */

namespace MarcNewton\LaravelASAL\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Locale;

class LocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        app()->setLocale($this->getAcceptedLocale($request));
        return $next($request);
    }
    /**
     * @param Request $request
     * @return String
     */
    private function getAcceptedLocale(Request $request): string
    {
        // Scrape available locales from resources/lang/
        $available = Cache::rememberForever(config('language.cache.name', 'resources.lang'), function () {

            return $this->scanLanguages();

        });

        // Standardise to hyphened separator for native use in javascript locale formatters.
        $preferences = array_map(function ($locale) {

            return str_replace('_','-', $locale);

        }, $request->getLanguages());

        # Exact match a language variant first or main language if listed in user preference.
        foreach ($preferences AS $preference) {

            # Return match else try users next preference.

            if (in_array($preference, $available))
                return $preference;
        }


        $user = $request->user();

        // If is loaded user and has locale preference, prepend choice to browsers locale preferences list.
        if($user && !empty($user->locale))
            array_unshift($preferences, $user->locale);

        reset($preferences);

        # No exact match found, Perform a Loose match for a main origin language.
        foreach ($preferences AS $preference) {

            # Find a close match for current preference.

            $preference = Locale::lookup($available, $preference);

            if (!empty($preference))
                return $preference;
        }

        # No language preference found, return config default or English if not set.
        return config('app.locale', 'en');
    }

    /**
     * Scan languages folder and return list of available languages
     * .
     * @return array
     */
    private function scanLanguages() {

        return array_diff(scandir(resource_path('lang')), ['..', '.']) ?: [];

    }
}
