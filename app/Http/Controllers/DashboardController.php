<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\LoginLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('dashboard.view');

        $activeUsers = User::where('is_active', true)->count();
        $loginsToday = LoginLog::where('status', 'SUCCESS')->whereDate('login_at', today())->count();
        $failedLoginsToday = LoginLog::where('status', 'FAILED')->whereDate('created_at', today())->count();
        $recentLogins = LoginLog::with('user')->latest('created_at')->limit(10)->get();

        $canViewTickets = $request->user()->hasPermission('ticket.view');

        $openTickets = 0;
        $criticalTickets = 0;
        $escalatedTickets = 0;
        $slaBreached = 0;
        $recentTickets = collect();

        if ($canViewTickets) {
            $openTickets = Ticket::whereNotIn('status', Ticket::CLOSED_STATUSES)->count();
            $criticalTickets = Ticket::whereNotIn('status', Ticket::CLOSED_STATUSES)->where('priority', 'CRITICAL')->count();
            $escalatedTickets = Ticket::where('status', 'ESCALATED')->count();

            $slaBreached = Ticket::whereNotIn('status', Ticket::CLOSED_STATUSES)
                ->whereNotNull('sla_due_at')
                ->where('sla_due_at', '<', now())
                ->count()
                + Ticket::whereIn('status', Ticket::CLOSED_STATUSES)
                    ->whereNotNull('sla_due_at')
                    ->whereRaw('COALESCE(resolved_at, closed_at) > sla_due_at')
                    ->count();

            $recentTickets = Ticket::with('pic')->latest('created_at')->limit(5)->get();
        }

        $canViewInvoices = $request->user()->hasPermission('invoice.view');

        $unpaidInvoices = 0;
        $overdueInvoices = 0;
        $unpaidTotal = 0;
        $recentInvoices = collect();

        if ($canViewInvoices) {
            $unpaidInvoices = Invoice::where('status', 'UNPAID')->count();
            $overdueInvoices = Invoice::where('status', 'UNPAID')->whereDate('due_date', '<', now())->count();
            $unpaidTotal = Invoice::where('status', 'UNPAID')->sum('total');
            $recentInvoices = Invoice::with('customer')->latest('created_at')->limit(5)->get();
        }

        return view('dashboard.index', compact(
            'activeUsers', 'loginsToday', 'failedLoginsToday', 'recentLogins',
            'canViewTickets', 'openTickets', 'criticalTickets', 'escalatedTickets', 'slaBreached', 'recentTickets',
            'canViewInvoices', 'unpaidInvoices', 'overdueInvoices', 'unpaidTotal', 'recentInvoices',
        ));
    }
}
