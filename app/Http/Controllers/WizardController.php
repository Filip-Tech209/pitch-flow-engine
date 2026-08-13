<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Competition;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WizardController extends Controller
{
    public function getTeams()
    {
        return response()->json(Team::select('id', 'name')->orderBy('name')->get());
    }

    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'required|string|max:10|unique:competitions,code',
            'type'       => 'required|in:LEAGUE,KNOCKOUT,HYBRID',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'team_ids'   => 'required|array|min:2',
            'team_ids.*' => 'exists:teams,id'
        ]);

        DB::beginTransaction();
        try {
            // 1. Create Competition
            $competition = Competition::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'type' => $validated['type'],
            ]);

            // 2. Create Active Season
            $seasonName = date('Y', strtotime($validated['start_date'])) . '/' . date('Y', strtotime($validated['end_date']));
            $season = Season::create([
                'competition_id' => $competition->id,
                'name'           => $seasonName,
                'start_date'     => $validated['start_date'],
                'end_date'       => $validated['end_date'],
                'is_active'      => true,
            ]);

            // 3. Attach Selected Teams to Season
            foreach ($validated['team_ids'] as $teamId) {
                $season->teams()->attach($teamId, [
                    'id' => (string) Str::uuid()
                ]);
            }

            DB::commit();

            return response()->json([
                'success'        => true,
                'message'        => 'Competition and Season initialized successfully.',
                'competition_id' => $competition->id,
                'season_id'      => $season->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}