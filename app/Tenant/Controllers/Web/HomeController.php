<?php

declare(strict_types=1);

namespace App\Tenant\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        if (tenant('store_type') === 'resto') return view('pages.tenant.store.resto.index');
        return view('pages.tenant.store.retail.index');

    }
}
