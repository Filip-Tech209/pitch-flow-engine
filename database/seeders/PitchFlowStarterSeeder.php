<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Season;
use App\Models\Team;
use App\Models\Player;
use App\Models\PlayerRegistration;
use App\Models\SeasonTeam;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PitchFlowStarterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Competition & Active Season
        $competition = Competition::create([
            'name' => 'Default  League',
            'code' => 'DEFAULT',
            'type' => 'LEAGUE',
        ]);

        $season = Season::create([
            'competition_id' => $competition->id,
            'name' => 'DEFAULT',
            'start_date' => '2026-08-15',
            'end_date' => '2027-05-25',
            'is_active' => false,
        ]);

        // 2. 20 Pre-defined Premier Clubs
        $teamsData = [
            ['name' => 'Arsenal FC', 'short' => 'ARS', 'primary' => '#EF0107', 'secondary' => '#FFFFFF'],
            ['name' => 'Aston Villa', 'short' => 'AVL', 'primary' => '#95BFE6', 'secondary' => '#670E36'],
            ['name' => 'AFC Bournemouth', 'short' => 'BOU', 'primary' => '#DA291C', 'secondary' => '#000000'],
            ['name' => 'Brentford FC', 'short' => 'BRE', 'primary' => '#E30613', 'secondary' => '#F9A01B'],
            ['name' => 'Brighton & Hove Albion', 'short' => 'BHA', 'primary' => '#0057B8', 'secondary' => '#FFFFFF'],
            ['name' => 'Chelsea FC', 'short' => 'CHE', 'primary' => '#034694', 'secondary' => '#DBA111'],
            ['name' => 'Crystal Palace', 'short' => 'CRY', 'primary' => '#1B458F', 'secondary' => '#C41230'],
            ['name' => 'Everton FC', 'short' => 'EVE', 'primary' => '#003399', 'secondary' => '#FFFFFF'],
            ['name' => 'Fulham FC', 'short' => 'FUL', 'primary' => '#FFFFFF', 'secondary' => '#000000'],
            ['name' => 'Ipswich Town', 'short' => 'IPS', 'primary' => '#0000FF', 'secondary' => '#FFFFFF'],
            ['name' => 'Leicester City', 'short' => 'LEI', 'primary' => '#0053A0', 'secondary' => '#FDBE11'],
            ['name' => 'Liverpool FC', 'short' => 'LIV', 'primary' => '#C8102E', 'secondary' => '#00B2A9'],
            ['name' => 'Manchester City', 'short' => 'MCI', 'primary' => '#6CABDD', 'secondary' => '#1C2C5B'],
            ['name' => 'Manchester United', 'short' => 'MUN', 'primary' => '#DA020E', 'secondary' => '#FFE500'],
            ['name' => 'Newcastle United', 'short' => 'NEW', 'primary' => '#241F20', 'secondary' => '#FFFFFF'],
            ['name' => 'Nottingham Forest', 'short' => 'NFO', 'primary' => '#DD0000', 'secondary' => '#FFFFFF'],
            ['name' => 'Southampton FC', 'short' => 'SOU', 'primary' => '#D1242A', 'secondary' => '#111111'],
            ['name' => 'Tottenham Hotspur', 'short' => 'TOT', 'primary' => '#132257', 'secondary' => '#FFFFFF'],
            ['name' => 'West Ham United', 'short' => 'WHU', 'primary' => '#7A263A', 'secondary' => '#1BB1E7'],
            ['name' => 'Wolverhampton Wanderers', 'short' => 'WOL', 'primary' => '#FDB913', 'secondary' => '#231F20'],
        ];

        $firstNames = ['Marcus', 'Liam', 'Ethan', 'Noah', 'Oliver', 'Lucas', 'Mason', 'Logan', 'Alexander', 'James', 'Benjamin', 'Mateo', 'Daniel', 'David', 'Joseph'];
        $lastNames = ['Mwaura', 'Silva', 'Sterling', 'Walker', 'De Bruyne', 'Saka', 'Mainoo', 'Palmer', 'Rice', 'Alexander', 'Foden', 'Diaz', 'Gakpo', 'Saliba', 'Trippier'];
        $positions = ['GK', 'DF', 'DF', 'DF', 'DF', 'MF', 'MF', 'MF', 'MF', 'FW', 'FW', 'FW', 'GK', 'DF', 'FW'];

        foreach ($teamsData as $teamInfo) {
            // Create Team
            $team = Team::create([
                'name' => $teamInfo['name'],
                'short_name' => $teamInfo['short'],
                'primary_color' => $teamInfo['primary'],
                'secondary_color' => $teamInfo['secondary'],
                'default_formation' => '4-3-3',
            ]);

            // Register Team into active Season
            SeasonTeam::create([
                'season_id' => $season->id,
                'team_id' => $team->id,
            ]);

            // Create 15 Players & Register each to the team
            for ($i = 0; $i < 15; $i++) {
                $firstName = $firstNames[$i];
                $lastName = $lastNames[array_rand($lastNames)];

                $player = Player::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'dob' => rand(1995, 2007) . '-' . sprintf('%02d', rand(1, 12)) . '-' . sprintf('%02d', rand(1, 28)),
                    'national_id_passport' => 'ID-' . Str::upper(Str::random(8)),
                    'nationality' => 'Kenya',
                    'foot' => (rand(1, 10) > 2) ? 'right' : 'left',
                ]);

                PlayerRegistration::create([
                    'season_id' => $season->id,
                    'team_id' => $team->id,
                    'player_id' => $player->id,
                    'jersey_number' => $i + 1,
                    'position' => $positions[$i],
                    'height_cm' => rand(170, 195),
                    'weight_kg' => rand(68, 88),
                ]);
            }
        }
    }
}