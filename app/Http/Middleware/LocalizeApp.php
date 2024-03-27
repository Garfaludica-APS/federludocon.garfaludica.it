<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LocalizeApp
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
	 */
	public function handle(Request $request, Closure $next, string $domain = 'pub'): Response
	{
		$locale = Config::get('app.locale', 'it');
		$headerLang = $request->getPreferredLanguage(['it', 'en']);

		switch ($domain) {
			case 'admin':
				$locale = $request->cookie('lang', $headerLang ?? $locale);
				break;
			case 'pub':
			default:
				if (Str::startsWith($request->route()->getName(), 'en.'))
					$locale = 'en';
				else
					$locale = $request->cookie('lang', $headerLang ?? $locale);
				break;
		}

		App::setLocale($locale);
		$locale = App::currentLocale();

		if (!$request->hasCookie('lang') || $request->cookie('lang') !== $locale)
			Cookie::queue('lang', $locale, 60 * 24 * 365, null, null, null, false);

		if ($domain === 'admin')
			return $next($request);
		if ($locale === 'en' && !Str::startsWith($request->route()->getName(), 'en.'))
			return redirect()->route('en.' . $request->route()->getName(), $request->route()->parameters());
		if ($locale === 'it' && Str::startsWith($request->route()->getName(), 'en.'))
			return redirect()->route(Str::substr($request->route()->getName(), 3), $request->route()->parameters());

		return $next($request);
	}
}
