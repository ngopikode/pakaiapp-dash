<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        if (tenant('store_type') === 'retail') {
            return view('pages.tenant.retail.index');
        }

        return view('pages.tenant.store.resto.index');
    }
}
