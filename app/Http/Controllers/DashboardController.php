<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Appointment;
use App\Models\Treatment;
use App\Models\User;
use App\Models\PageView;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $appointments = match($user->role) {
            Role::Owner    => Appointment::with(['user', 'employee', 'treatments'])->get(),
            Role::Employee => Appointment::with(['user', 'employee', 'treatments'])->where('employee_id', $user->id)->get(),
            Role::Customer => Appointment::with(['employee', 'treatments'])->where('user_id', $user->id)->get(),
        };

        $treatments = null;
        $employees  = null;
        $totalViews = null;
        $todayViews = null;
        $customerVisits = null;
        $salesPeriodStart = null;
        $salesPeriodEnd = null;
        $salesChartLabels = null;
        $salesChartTotals = null;
        $salesPeriodTotal = null;
        $allOrders = null;

        if ($user->role === Role::Owner) {
            $treatments = Treatment::all();
            $employees  = User::where('role', Role::Employee)->get();
            $totalViews = PageView::count();
            $todayViews = PageView::whereDate('created_at', today())->count();

            $customerVisits = User::where('role', Role::Customer)
                ->withCount('pageViews')
                ->orderByDesc('page_views_count')
                ->get();

            $allOrders = Order::with(['products', 'user'])
                ->orderByDesc('date')
                ->get();

            $salesPeriodStart = $request->filled('from')
                ? Carbon::parse($request->query('from'))->startOfDay()
                : now()->subDays(29)->startOfDay();

            $salesPeriodEnd = $request->filled('to')
                ? Carbon::parse($request->query('to'))->endOfDay()
                : now()->endOfDay();

            $orders = Order::whereBetween('date', [$salesPeriodStart->toDateString(), $salesPeriodEnd->toDateString()])
                ->orderBy('date')
                ->get()
                ->groupBy(fn($order) => $order->date->format('Y-m-d'));

            $salesChartLabels = [];
            $salesChartTotals = [];

            $cursor = $salesPeriodStart->copy();
            while ($cursor->lte($salesPeriodEnd)) {
                $key = $cursor->format('Y-m-d');
                $salesChartLabels[] = $cursor->format('d-m');
                $salesChartTotals[] = $orders->get($key, collect())->sum('price') / 100;
                $cursor->addDay();
            }

            $salesPeriodTotal = array_sum($salesChartTotals);
        }

        return view('dashboard', compact(
            'appointments',
            'treatments',
            'employees',
            'totalViews',
            'todayViews',
            'customerVisits',
            'salesPeriodStart',
            'salesPeriodEnd',
            'salesChartLabels',
            'salesChartTotals',
            'salesPeriodTotal',
            'allOrders'
        ));
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->route('dashboard')->with('success', 'Afspraak verwijderd.');
    }
}