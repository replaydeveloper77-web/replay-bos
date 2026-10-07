<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketTitleRequest;
use App\Http\Requests\UpdateTicketTitleRequest;
use App\Models\Ticket;
use App\Models\TicketTitle;
use Illuminate\Http\Request;

class TicketTitleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', TicketTitle::class);

        $titles = TicketTitle::when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->string('q')->trim() . '%';
                $q->where('name', 'like', $term)
                  ->orWhere('category', 'like', $term);
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('ticket-titles.index', [
            'titles' => $titles,
        ]);
    }

    public function create()
    {
        $this->authorize('create', TicketTitle::class);

        return view('ticket-titles.create', [
            'categories' => Ticket::CATEGORIES,
            'priorities' => Ticket::PRIORITIES,
        ]);
    }

    public function store(StoreTicketTitleRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['created_by'] = $request->user()->id;

        TicketTitle::create($data);

        return redirect()->route('ticket-titles.index')->with('status', 'Judul tiket master data berhasil ditambahkan.');
    }

    public function edit(TicketTitle $ticketTitle)
    {
        $this->authorize('update', $ticketTitle);

        return view('ticket-titles.edit', [
            'title' => $ticketTitle,
            'categories' => Ticket::CATEGORIES,
            'priorities' => Ticket::PRIORITIES,
        ]);
    }

    public function update(UpdateTicketTitleRequest $request, TicketTitle $ticketTitle)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $ticketTitle->update($data);

        return redirect()->route('ticket-titles.index')->with('status', 'Judul tiket master data berhasil diperbarui.');
    }

    public function destroy(TicketTitle $ticketTitle)
    {
        $this->authorize('delete', $ticketTitle);

        $ticketTitle->delete();

        return redirect()->route('ticket-titles.index')->with('status', 'Judul tiket master data berhasil dihapus.');
    }

    public function toggle(TicketTitle $ticketTitle)
    {
        $this->authorize('update', $ticketTitle);

        $ticketTitle->update(['is_active' => ! $ticketTitle->is_active]);

        $status = $ticketTitle->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('ticket-titles.index')->with('status', "Judul tiket berhasil {$status}.");
    }
}
