<?php

namespace App\Http\Controllers;

use App\Http\Requests\CloseTroubleshootingSessionRequest;
use App\Http\Requests\StoreTroubleshootingSessionRequest;
use App\Http\Requests\StoreTroubleshootingStepRequest;
use App\Models\Ticket;
use App\Models\TroubleshootingSession;
use App\Services\TroubleshootingService;

class TroubleshootingController extends Controller
{
    public function __construct(private TroubleshootingService $troubleshootingService)
    {
    }

    public function store(StoreTroubleshootingSessionRequest $request, Ticket $ticket)
    {
        $this->troubleshootingService->startSession($ticket, $request->validated(), $request->user(), $request);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Sesi troubleshooting dimulai.');
    }

    public function addStep(StoreTroubleshootingStepRequest $request, TroubleshootingSession $session)
    {
        $this->troubleshootingService->addStep($session, $request->validated()['step_type'], $request->validated()['description'], $request->user(), $request);

        return redirect()->route('tickets.show', $session->ticket)->with('status', 'Step troubleshooting ditambahkan.');
    }

    public function close(CloseTroubleshootingSessionRequest $request, TroubleshootingSession $session)
    {
        $this->troubleshootingService->closeSession($session, $request->validated()['conclusion'], $request->user(), $request);

        return redirect()->route('tickets.show', $session->ticket)->with('status', 'Sesi troubleshooting ditutup.');
    }
}
