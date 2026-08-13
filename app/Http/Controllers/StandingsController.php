<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Season;
use App\Models\MatchEvent;
use App\Models\GameMatch;
use Illuminate\Support\Facades\DB;

class StandingsController extends Controller
{
    public function getStandings(Request $request)
    {
        $request->validate(['competition_id' => 'required|uuid']);

        $season = Season::with('teams')->where('competition_id', $request->competition_id)->where('is_active', true)->first();

        if (!$season) {
            return response()->json(['standings' => [], 'golden_boot' => []]);
        }

        // 1. Calculate Standings
        $teams = $season->teams;
        $matches = GameMatch::where('season_id', $season->id)->where('status', 'FULL_TIME')->get();

        $table = [];
        foreach ($teams as $team) {
            $table[$team->id] = [
                'team_id' => $team->id,
                'name'    => $team->name,
                'played'  => 0,
                'won'     => 0,
                'drawn'   => 0,
                'lost'    => 0,
                'gf'      => 0,
                'ga'      => 0,
                'gd'      => 0,
                'points'  => 0,
            ];
        }

        foreach ($matches as $m) {
            if (!isset($table[$m->home_team_id]) || !isset($table[$m->away_team_id])) continue;

            $table[$m->home_team_id]['played']++;
            $table[$m->away_team_id]['played']++;

            $table[$m->home_team_id]['gf'] += $m->home_score;
            $table[$m->home_team_id]['ga'] += $m->away_score;
            $table[$m->away_team_id]['gf']  += $m->away_score;
            $table[$m->away_team_id]['ga']  += $m->home_score;

            if ($m->home_score > $m->away_score) {
                $table[$m->home_team_id]['won']++;
                $table[$m->home_team_id]['points'] += 3;
                $table[$m->away_team_id]['lost']++;
            } elseif ($m->home_score < $m->away_score) {
                $table[$m->away_team_id]['won']++;
                $table[$m->away_team_id]['points'] += 3;
                $table[$m->home_team_id]['lost']++;
            } else {
                $table[$m->home_team_id]['drawn']++;
                $table[$m->away_team_id]['drawn']++;
                $table[$m->home_team_id]['points'] += 1;
                $table[$m->away_team_id]['points'] += 1;
            }
        }

        foreach ($table as &$row) {
            $row['gd'] = $row['gf'] - $row['ga'];
        }

        // Sort by Points DESC, then Goal Difference DESC
        usort($table, function ($a, $b) {
            if ($a['points'] === $b['points']) {
                return $b['gd'] <=> $a['gd'];
            }
            return $b['points'] <=> $a['points'];
        });

        // 2. Auto-Generate Golden Boot Rankings
        $goldenBoot = MatchEvent::select('player_id', DB::raw('count(*) as goals'))
            ->whereHas('match', function ($q) use ($season) {
                $q->where('season_id', $season->id);
            })
            ->where('type', 'GOAL')
            ->whereNotNull('player_id')
            ->groupBy('player_id')
            ->orderBy('goals', 'desc')
            ->with(['player.team'])
            ->take(10)
            ->get();

        return response()->json([
            'standings'   => array_values($table),
            'golden_boot' => $goldenBoot
        ]);
    }
}