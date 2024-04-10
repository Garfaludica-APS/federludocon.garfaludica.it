<?php

namespace App\Http\Controllers;

use Emargareten\InertiaModal\Modal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ModalController extends Controller
{
	public function privacy(Request $request): Modal
	{
		return $this->show($request, 'Privacy');
	}

	public function terms(Request $request): Modal
	{
		return $this->show($request, 'Terms');
	}

	public function refund(Request $request): Modal
	{
		return $this->show($request, 'Refund');
	}

	protected function show(Request $request, string $name): Modal
	{
		$defaultBase = App::isLocale('it') ? 'home' : 'en.home';
		$base = $request->input('redirect', $defaultBase);
		if (Str::startsWith($base, ['modal.', 'en.modal.']))
			$base = $defaultBase;
		return Inertia::modal($name)->baseRoute($base);
	}
}
