<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Status;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StatusController extends Controller
{
    use LogsActivity;

    public function store(Request $request, Board $board)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string',
            'wip_limit' => 'nullable|integer|min:0',
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while ($board->statuses()->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $status = $board->statuses()->create([
            'name' => $request->name,
            'color' => $request->color ?? '#6c757d',
            'wip_limit' => $request->wip_limit,
            'slug' => $slug,
            'position' => $board->statuses()->max('position') + 1,
        ]);

        $this->logActivity('created_status', "Created column: {$status->name} on board: {$board->name}", $status, [
            'board_id' => $board->id,
            'color' => $status->color,
            'wip_limit' => $status->wip_limit,
        ]);

        return response()->json(['success' => true, 'data' => $status]);
    }

    public function show(Board $board, Status $status)
    {
        return response()->json($status);
    }

    public function update(Request $request, Board $board, Status $status)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string',
            'wip_limit' => 'nullable|integer|min:0',
        ]);

        $oldValues = $status->only(['name', 'color', 'wip_limit']);
        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while ($board->statuses()->where('slug', $slug)->where('id', '!=', $status->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $status->update([
            'name' => $request->name,
            'color' => $request->color ?? $status->color,
            'wip_limit' => $request->wip_limit,
            'slug' => $slug,
        ]);

        $this->logActivity('updated_status', "Updated column: {$status->name} on board: {$board->name}", $status, [
            'old' => $oldValues,
            'new' => $request->only(['name', 'color', 'wip_limit']),
            'board_id' => $board->id,
        ]);

        return response()->json(['success' => true, 'data' => $status]);
    }

    public function destroy(Board $board, Status $status)
    {
        $name = $status->name;
        $status->delete();

        $this->logActivity('deleted_status', "Deleted column: {$name} from board: {$board->name}", null, [
            'board_id' => $board->id,
        ]);

        return response()->json(['success' => true]);
    }

    // public function setWIPLimit(Request $request, Board $board, Status $status)
    // {
    //     $request->validate(['wip_limit' => 'nullable|integer|min:0']);
    //     $oldWipLimit = $status->wip_limit;
    //     $status->update(['wip_limit' => $request->wip_limit]);

    //     $this->logActivity('updated_wip_limit', "Set WIP limit to {$request->wip_limit} for column: {$status->name} on board: {$board->name}", $status, [
    //         'old_wip_limit' => $oldWipLimit,
    //         'new_wip_limit' => $request->wip_limit,
    //         'board_id' => $board->id,
    //     ]);

    //     return response()->json(['success' => true, 'data' => $status]);
    // }
}
