<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeamsController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // $this->authorizeResource(Team::class, 'team');
    }

    public function index()
    {
        $teams = Auth::user()->teams()->with('users')->get();
        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        return view('teams.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $team = Team::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
        ]);

        $team->users()->attach(Auth::id(), ['role' => 'owner']);

        $this->logActivity('created_team', "Created team: {$team->name}", $team);

        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        $team->load('users');
        return view('teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        return view('teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate(['name' => 'required|string|max:255']);

        $oldValues = $team->only(['name']);
        $team->update($request->only('name'));

        $this->logActivity('updated_team', "Updated team: {$team->name}", $team, [
            'old' => $oldValues,
            'new' => $validated,
        ]);

        return redirect()->route('teams.index')->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $name = $team->name;
        $team->delete();
        $this->logActivity('deleted_team', "Deleted team: {$name}", null);
        return redirect()->route('teams.index')->with('success', 'Team deleted successfully.');
    }

    public function addMember(Request $request, Team $team)
    {
        $this->authorize('update', $team);

        $validated = $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:member,admin',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        if ($team->users()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['email' => 'User is already a member of this team.']);
        }

        $team->users()->attach($user->id, ['role' => $request->role]);

        $this->logActivity('added_team_member', "Added {$user->name} to team: {$team->name}", $team, [
            'role' => $validated['role'],
        ]);

        return redirect()->route('teams.show', $team)->with('success', 'Member added successfully.');
    }

    public function updateMember(Request $request, Team $team, $userId)
    {
        $this->authorize('update', $team);

        $validated = $request->validate(['role' => 'required|in:member,admin']);

        $user = User::findOrFail($userId);
        if ($team->users()->where('user_id', $user->id)->wherePivot('role', 'owner')->exists()) {
            return back()->withErrors(['role' => 'Cannot change the owner\'s role.']);
        }
        $oldRole = $team->users()->where('user_id', $userId)->first()->pivot->role;
        $team->users()->updateExistingPivot($user->id, ['role' => $request->role]);

        $this->logActivity('updated_team_member_role', "Updated role for {$user->name} in team: {$team->name}", $team, [
            'old_role' => $oldRole,
            'new_role' => $validated['role'],
        ]);

        return redirect()->route('teams.show', $team)->with('success', 'Member role updated successfully.');
    }

    public function removeMember(Team $team, $userId)
    {
        $this->authorize('update', $team);

        $user = User::findOrFail($userId);
        if ($team->user_id === $user->id) {
            return back()->withErrors(['remove' => 'Cannot remove the team owner.']);
        }

        $team->users()->detach($user->id);
        $this->logActivity('removed_team_member', "Removed {$user->name} from team: {$team->name}", $team);
        return redirect()->route('teams.show', $team)->with('success', 'Member removed successfully.');
    }
}
