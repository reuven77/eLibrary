<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly AdminDashboardService $dashboard)
    {
    }

    public function __invoke(): View
    {
        return view('pages.admin.dashboard', [
            'summary' => $this->dashboard->summary(),
            'loans' => $this->dashboard->recentLoans(),
        ]);
    }
}
