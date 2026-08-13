<div x-show="activeTab === 'league'" x-cloak class="space-y-6">
    
    <!-- Header Banner & Action Control -->
    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                League & Fixture Management
            </h2>
            <p class="text-xs text-slate-500 mt-1">Automated round-robin scheduling & match control center.</p>
        </div>

        <!-- Action Button -->
        <template x-if="leagueMatches.length === 0 && selectedCompetitionId">
            <button @click="generateFixtures()" 
                    :disabled="isGenerating"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-blue-600 text-white font-bold text-xs uppercase tracking-wider hover:opacity-95 shadow-md disabled:opacity-50 transition-all flex items-center space-x-2">
                <span x-show="isGenerating" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <span x-text="isGenerating ? 'Generating Schedule...' : '⚡ Generate League Schedule'"></span>
            </button>
        </template>
    </div>

    <!-- SIMULATED LOADING OVERLAY / STATE -->
    <div x-show="isGenerating" x-cloak class="p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border border-emerald-500/30 shadow-lg space-y-4 transition-all">
        <div class="relative w-16 h-16 mx-auto flex items-center justify-center">
            <div class="absolute inset-0 rounded-full border-4 border-emerald-500/20 border-t-emerald-500 animate-spin"></div>
            <span class="text-2xl animate-bounce">⚽</span>
        </div>
        <div class="space-y-2">
            <h3 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">
                Generating Fixtures & Match Slots...
            </h3>
            <p class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 animate-pulse" x-text="loadingStepMessage || 'Calculating round-robin match pairings...'"></p>
            <p class="text-[11px] text-slate-400">Assigning venues and 1.5-hour time windows...</p>
        </div>
    </div>

    <!-- First Time / Empty State Prompt (Hidden during generation) -->
    <div x-show="leagueMatches.length === 0 && !isGenerating" class="p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border-2 border-dashed border-slate-200 dark:border-slate-800 space-y-4 shadow-sm">
        <div class="w-16 h-16 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center mx-auto text-2xl font-bold">
            ⚽
        </div>
        <div class="space-y-1">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wide">No Fixtures Generated Yet</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto">
                Select or confirm your competition and click below to automatically generate round-robin match schedules, complete with venues and 1.5-hour time slots.
            </p>
        </div>
        <button x-show="selectedCompetitionId"
                @click="generateFixtures()" 
                :disabled="isGenerating"
                class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider shadow-md transition-all disabled:opacity-50 inline-flex items-center space-x-2">
            <span>⚡ Generate Schedule Now</span>
        </button>
    </div>

    <!-- Scrollable Vertical Fixture Feed (Shown once matches exist) -->
    <div x-show="leagueMatches.length > 0 && !isGenerating" class="space-y-4">
        
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                Scheduled Fixtures (<span x-text="leagueMatches.length"></span>)
            </h3>
            <span class="text-[11px] font-medium text-slate-400">Scroll to view all matches</span>
        </div>

        <!-- Scrollable Container -->
        <div class="max-h-[620px] overflow-y-auto pr-2 space-y-3 custom-scrollbar">
            <template x-for="match in leagueMatches" :key="match.id">
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-emerald-500/40 transition-all flex flex-col md:flex-row items-center justify-between gap-4">
                    
                    <!-- Left: Venue & Time Window (1.5 Hour Span) -->
                    <div class="flex items-center space-x-3 w-full md:w-1/3 shrink-0 border-b md:border-b-0 md:border-r border-slate-100 dark:border-slate-800 pb-2 md:pb-0">
                        <div class="p-2 rounded-xl bg-slate-100 dark:bg-slate-950 text-slate-600 dark:text-slate-400 text-xs text-center min-w-[50px]">
                            <p class="font-black text-xs text-emerald-600 dark:text-emerald-400" x-text="formatMatchDate(match.kickoff_time || match.kickoff_at).day"></p>
                            <p class="text-[10px] uppercase font-bold text-slate-400" x-text="formatMatchDate(match.kickoff_time || match.kickoff_at).month"></p>
                        </div>
                        <div class="space-y-0.5">
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center space-x-1">
                                <span>🕒</span>
                                <span x-text="formatMatchTimeWindow(match.kickoff_time || match.kickoff_at)"></span>
                            </p>
                            <p class="text-[11px] font-semibold text-slate-400 flex items-center space-x-1 truncate">
                                <span>📍</span>
                                <span x-text="match.venue ? match.venue.name : (match.home_team ? match.home_team.stadium || 'Main Pitch' : 'Main Pitch')"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Center: Team A vs Team B -->
                    <div class="flex items-center justify-between w-full md:w-2/5 px-2">
                        <!-- Home Team -->
                        <div class="flex items-center space-x-2 text-right w-5/12 justify-end">
                            <span class="text-xs font-black text-slate-800 dark:text-slate-200 truncate" x-text="match.home_team ? match.home_team.name : 'TBD'"></span>
                            <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-500 shrink-0">
                                <span x-text="match.home_team ? match.home_team.name.charAt(0) : 'H'"></span>
                            </div>
                        </div>

                        <!-- VS / Score -->
                        <div class="text-center px-3">
                            <template x-if="match.status === 'SCHEDULED'">
                                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-400 font-bold text-[10px]">VS</span>
                            </template>
                            <template x-if="match.status !== 'SCHEDULED'">
                                <span class="text-sm font-black text-emerald-600 dark:text-emerald-400" x-text="(match.home_score || 0) + ' - ' + (match.away_score || 0)"></span>
                            </template>
                        </div>

                        <!-- Away Team -->
                        <div class="flex items-center space-x-2 text-left w-5/12 justify-start">
                            <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-xs font-bold text-slate-500 shrink-0">
                                <span x-text="match.away_team ? match.away_team.name.charAt(0) : 'A'"></span>
                            </div>
                            <span class="text-xs font-black text-slate-800 dark:text-slate-200 truncate" x-text="match.away_team ? match.away_team.name : 'TBD'"></span>
                        </div>
                    </div>

                    <!-- Right: Match Status & Start Control -->
                    <div class="flex items-center justify-end space-x-3 w-full md:w-1/4 shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                        <span :class="{
                                  'bg-amber-500/10 text-amber-600 border border-amber-500/20': match.status === 'SCHEDULED',
                                  'bg-emerald-500/10 text-emerald-500 animate-pulse border border-emerald-500/20': match.status.includes('LIVE'),
                                  'bg-slate-500/10 text-slate-400 border border-slate-500/20': match.status === 'FULL_TIME'
                              }"
                              class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider shrink-0" 
                              x-text="match.status.replace('_', ' ')">
                        </span>

                        <template x-if="match.status === 'SCHEDULED'">
                            <button @click="startMatch(match)" 
                                    class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold uppercase tracking-wider shadow-sm transition-all shrink-0 flex items-center space-x-1">
                                <span>▶ Start Match</span>
                            </button>
                        </template>

                        <template x-if="match.status.includes('LIVE')">
                            <button @click="activeLiveMatch = match; activeTab = 'matches'" 
                                    class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold uppercase tracking-wider shadow-sm transition-all shrink-0">
                                Control Console &rarr;
                            </button>
                        </template>
                    </div>

                </div>
            </template>
        </div>

    </div>
</div>