<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Season;
use App\Models\GameMatch;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FixtureController extends Controller
{
    public function getFixtures(Request $request)
    {
        $request->validate(['competition_id' => 'required|uuid']);

        $season = Season::where('competition_id', $request->competition_id)
            ->where('is_active', true)
            ->first();

        if (!$season) {
            return response()->json(['matches' => [], 'season' => null]);
        }

        $matches = GameMatch::with(['homeTeam', 'awayTeam', 'events.player'])
            ->where('season_id', $season->id)
            ->orderBy('kickoff_time', 'asc')
            ->get();

        return response()->json([
            'season'  => $season,
            'matches' => $matches,
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate(['competition_id' => 'required|uuid']);

        $season = Season::with('teams')
            ->where('competition_id', $request->competition_id)
            ->where('is_active', true)
            ->firstOrFail();

        if (GameMatch::where('season_id', $season->id)->exists()) {
            return response()->json(['message' => 'Fixtures already exist for this season.'], 422);
        }

        $teams = $season->teams->pluck('id')->toArray();

        if (count($teams) < 2) {
            return response()->json(['message' => 'At least 2 teams are required to generate fixtures.'], 422);
        }

        // Add dummy team if odd count (for BYE rounds)
        if (count($teams) % 2 !== 0) {
            $teams[] = null;
        }

        $numTeams = count($teams);
        $numRounds = $numTeams - 1;
        $matchesPerRound = $numTeams / 2;

        $firstLegMatches = [];
        $startDate = Carbon::now()->addDays(1)->setHour(14)->setMinute(0)->setSecond(0);
        $currentDate = $startDate->copy();

        // 1. Generate First Leg (Round Robin)
        for ($round = 0; $round < $numRounds; $round++) {
            $slotTime = $currentDate->copy();

            for ($i = 0; $i < $matchesPerRound; $i++) {
                $home = $teams[$i];
                $away = $teams[$numTeams - 1 - $i];

                if ($home !== null && $away !== null) {
                    $firstLegMatches[] = [
                        'id'           => (string) Str::uuid(),
                        'season_id'    => $season->id,
                        'home_team_id' => $home,
                        'away_team_id' => $away,
                        'venue_id'     => null,
                        'kickoff_time' => $slotTime->toDateTimeString(),
                        'status'       => 'SCHEDULED',
                        'home_score'   => 0,
                        'away_score'   => 0,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];

                    // Space matches 90 minutes apart on matchday
                    $slotTime->addMinutes(90);
                }
            }

            // Rotate array for next round (keep index 0 fixed)
            array_splice($teams, 1, 0, array_pop($teams));
            $currentDate->addDays(7); // Next matchday next week
        }

        // 2. Generate Second Leg (Reverse Home & Away)
        $secondLegMatches = [];
        foreach ($firstLegMatches as $match) {
            $secondLegMatches[] = [
                'id'           => (string) Str::uuid(),
                'season_id'    => $match['season_id'],
                'home_team_id' => $match['away_team_id'], // Reversed
                'away_team_id' => $match['home_team_id'], // Reversed
                'venue_id'     => null,
                'kickoff_time' => Carbon::parse($match['kickoff_time'])->addWeeks($numRounds)->toDateTimeString(),
                'status'       => 'SCHEDULED',
                'home_score'   => 0,
                'away_score'   => 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }

        $allMatches = array_merge($firstLegMatches, $secondLegMatches);

        // 3. Save into Database
        GameMatch::insert($allMatches);

        $count = count($allMatches);

        return response()->json([
            'success' => true, 
            'message' => "Successfully generated {$count} fixtures!"
        ]);
    }
}