<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\FieldVisitReport;
use App\Models\Ticket;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class PortalTeknisiController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('teknisi.portal.view');

        $user = $request->user();
        $employee = $user->employee;
        $myTeams = $user->teams;
        $myTeamIds = $myTeams->pluck('id')->all();
        $isSuperAdmin = $user->hasRole('super-admin');

        // 1. Work Orders
        $woQuery = WorkOrder::with(['customer', 'team', 'assignedTo'])
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('created_at', 'desc');

        if (! $isSuperAdmin) {
            $woQuery->where(function ($q) use ($user, $myTeamIds) {
                $q->where('assigned_to_id', $user->id);
                if (! empty($myTeamIds)) {
                    $q->orWhereIn('team_id', $myTeamIds);
                }
            });
        }

        $activeWorkOrders = (clone $woQuery)
            ->whereIn('status', ['SCHEDULED', 'IN_PROGRESS'])
            ->limit(15)
            ->get();

        $completedWorkOrdersCount = (clone $woQuery)
            ->where('status', 'DONE')
            ->count();

        // 2. Tickets (Perbaikan / Gangguan aktif)
        $ticketQuery = Ticket::with(['customer', 'team'])
            ->whereIn('status', ['OPEN', 'HANDLE', 'ESCALATED', 'PENDING'])
            ->latest('updated_at');

        if (! $isSuperAdmin) {
            if (! empty($myTeamIds)) {
                $ticketQuery->where(function ($q) use ($myTeamIds) {
                    $q->whereIn('team_id', $myTeamIds)
                        ->orWhereNull('team_id');
                });
            }
        }

        $activeTickets = $ticketQuery->limit(10)->get();

        // 3. Field Visits (Laporan Kunjungan Lapangan)
        $fieldVisitsQuery = FieldVisitReport::with(['customer', 'createdBy'])
            ->latest('visit_date');

        if (! $isSuperAdmin) {
            $fieldVisitsQuery->where('created_by', $user->id);
        }

        $recentVisits = $fieldVisitsQuery->limit(6)->get();

        // 4. Absensi Hari Ini
        $todayAttendance = null;
        if ($employee) {
            $todayAttendance = Attendance::where('employee_id', $employee->id)
                ->where('date', now()->toDateString())
                ->first();
        }

        // Summary counts
        $stats = [
            'scheduled_wo' => (clone $woQuery)->where('status', 'SCHEDULED')->count(),
            'in_progress_wo' => (clone $woQuery)->where('status', 'IN_PROGRESS')->count(),
            'done_wo' => $completedWorkOrdersCount,
            'active_tickets' => $activeTickets->count(),
            'my_visits' => FieldVisitReport::where('created_by', $user->id)->count(),
        ];

        return view('portal-teknisi.index', compact(
            'user',
            'employee',
            'myTeams',
            'activeWorkOrders',
            'activeTickets',
            'recentVisits',
            'todayAttendance',
            'stats'
        ));
    }
}
