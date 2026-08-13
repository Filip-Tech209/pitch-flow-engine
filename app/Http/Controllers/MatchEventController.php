<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameMatch;
use App\Models\MatchEvent;
use Illuminate\Support\Facades\DB;

class MatchEventController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $match = GameMatch::findOrFail($id);
        $match->status = $request->status;
        $match->save();

        return response()->json(['success' => true, 'match' => $match]);
    }

    public function recordEvent(Request $request, $matchId)
    {
        $validated = $request->validate([
            'team_id'   => 'required|uuid',
            'player_id' => 'nullable|uuid',
            'type'      => 'required|in:GOAL,YELLOW_CARD,RED_CARD,SUBSTITUTION,OWN_GOAL',
            'minute'    => 'required|integer|min:1|max:130'
        ]);

        DB::beginTransaction();
        try {
            $match = GameMatch::findOrFail($matchId);

            // Record Event
            $event = MatchEvent::create([
                'match_id'  => $match->id,
                'team_id'   => $validated['team_id'],
                'player_id' => $validated['player_id'],
                'type'      => $validated['type'],
                'minute'    => $validated['minute'],
            ]);

            // Update Score directly if GOAL
            if ($validated['type'] === 'GOAL') {
                if ($validated['team_id'] === $match->home_team_id) {
                    $match->increment('home_score');
                } else {
                    $match->increment('away_score');
                }
            } elseif ($validated['type'] === 'OWN_GOAL') {
                // Own goal increments opponent score
                if ($validated['team_id'] === $match->home_team_id) {
                    $match->increment('away_score');
                } else {
                    $match->increment('home_score');
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'event' => $event, 'match' => $match]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}