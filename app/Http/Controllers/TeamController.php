<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct(private TeamService $teamService)
    {
    }

    public function index()
    {
        $this->authorize('viewAny', Team::class);

        $teams = Team::withCount(['members', 'tickets'])->orderBy('name')->get();

        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        $this->authorize('create', Team::class);

        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('teams.create', ['users' => $users, 'team' => null]);
    }

    public function store(StoreTeamRequest $request)
    {
        $this->teamService->create($request->validated(), $request->user(), $request);

        return redirect()->route('teams.index')->with('status', 'Tim berhasil dibuat.');
    }

    public function edit(Team $team)
    {
        $this->authorize('update', Team::class);

        $team->load('members');
        $users = User::where('is_active', true)->orderBy('name')->get();

        return view('teams.edit', ['team' => $team, 'users' => $users]);
    }

    public function update(UpdateTeamRequest $request, Team $team)
    {
        $this->teamService->update($team, $request->validated(), $request->user(), $request);

        return redirect()->route('teams.index')->with('status', 'Tim berhasil diperbarui.');
    }

    public function disable(Request $request, Team $team)
    {
        $this->authorize('update', Team::class);

        $this->teamService->setActive($team, false, $request->user(), $request);

        return redirect()->route('teams.index')->with('status', 'Tim berhasil dinonaktifkan.');
    }

    public function enable(Request $request, Team $team)
    {
        $this->authorize('update', Team::class);

        $this->teamService->setActive($team, true, $request->user(), $request);

        return redirect()->route('teams.index')->with('status', 'Tim berhasil diaktifkan.');
    }
}
