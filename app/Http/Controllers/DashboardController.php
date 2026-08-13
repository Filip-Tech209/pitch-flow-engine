<?php

namespace App\Http\Controllers;

use App\Models\Season;
use App\Models\GameMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Generate double round-robin fixtures for the selected season.
     */
    public function generateFixtures(Request $request)
    {
        $request->validate([
            'competition_id' => 'required|uuid'
        ]);

        // 1. Fetch Season and registered teams
        $season = Season::with('teams')->find($request->competition_id);

        if (!$season) {
            return response()->json([
                'error' => 'The selected season/competition was not found.'
            ], 404);
        }

        $teams = $season->teams->pluck('id')->toArray();

        if (count($teams) < 2) {
            return response()->json([
                'error' => 'At least 2 teams are required to generate fixtures.'
            ], 422);
        }

        // Add dummy team if odd count (for BYE rounds)
        if (count($teams) % 2 !== 0) {
            $teams[] = null;
        }

        $numTeams = count($teams);
        $numRounds = $numTeams - 1;
        $matchesPerRound = $numTeams / 2;

        $firstLegMatches = [];
        $startDate = Carbon::now()->addDays(2)->setHour(14)->setMinute(0)->setSecond(0);
        $currentDate = $startDate->copy();

        // 2. Generate First Leg
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

        // 3. Generate Second Leg (Reverse Home & Away)
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

        // 4. Clear existing matches for this season and bulk insert new ones
        GameMatch::where('season_id', $season->id)->delete();
        GameMatch::insert($allMatches);

        return response()->json([
            'message' => 'Double round-robin schedule generated successfully!',
            'count'   => count($allMatches)
        ], 200);
    }
}