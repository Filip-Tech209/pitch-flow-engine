<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WizardController;
use App\Http\Controllers\FixtureController;
use App\Http\Controllers\MatchEventController;
use App\Http\Controllers\StandingsController;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

// Wizard Routes
Route::get('/api/teams', [WizardController::class, 'getTeams']);
Route::post('/api/competitions/initialize', [WizardController::class, 'initialize']);

// Dashboard & Fixture Routes
Route::get('/api/dashboard/fixtures', [FixtureController::class, 'getFixtures']);
Route::post('/api/dashboard/generate-fixtures', [FixtureController::class, 'generate']);

// Live Event Controls
Route::patch('/api/dashboard/matches/{id}/status', [MatchEventController::class, 'updateStatus']);
Route::post('/api/dashboard/matches/{id}/events', [MatchEventController::class, 'recordEvent']);

// Dynamic Standings & Top Scorers
Route::get('/api/dashboard/standings', [StandingsController::class, 'getStandings']);


Route::post('/dashboard/generate-fixtures', [DashboardController::class, 'generateFixtures']);