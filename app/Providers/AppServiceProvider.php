<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Competition;
use App\Models\Team;
use App\Models\Player;
use App\Models\GameMatch;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share Overview and Global data across views
        View::composer(['dashboard', 'welcome'], function ($view) {
            $view->with('globalData', [
                'total_teams'        => Team::count(),
                'total_players'      => Player::count(),
                'active_leagues'     => Competition::count(),
                'competitions'       => Competition::with(['seasons' => function($q) {
                                            $q->where('is_active', true);
                                        }])->get(),
                'latest_matches'     => GameMatch::with(['homeTeam', 'awayTeam'])
                                            ->latest()
                                            ->take(5)
                                            ->get()
            ]);
        });
    }
}