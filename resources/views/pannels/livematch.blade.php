<div x-show="activeTab === 'matches'" x-cloak class="space-y-6">
    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
        <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
            Live Match Console
        </h2>

        <!-- Active Live Match HUD -->
        <template x-if="activeLiveMatch">
            <div class="p-6 rounded-2xl bg-slate-900 text-white border border-slate-800 space-y-6 shadow-lg">
                <div class="flex items-center justify-between text-xs font-bold">
                    <span class="px-2.5 py-1 rounded bg-red-600 text-white animate-pulse tracking-widest">LIVE NOW</span>
                    <span class="text-slate-400" x-text="'Matchday ' + activeLiveMatch.match_day"></span>
                </div>

                <!-- Live Scoreboard -->
                <div class="flex items-center justify-around py-4">
                    <div class="text-center">
                        <p class="text-lg font-black" x-text="activeLiveMatch.home_team ? activeLiveMatch.home_team.name : 'Home'"></p>
                        <p class="text-4xl font-black text-emerald-400 mt-2" x-text="activeLiveMatch.home_score || 0"></p>
                    </div>
                    <span class="text-2xl font-bold text-slate-600">:</span>
                    <div class="text-center">
                        <p class="text-lg font-black" x-text="activeLiveMatch.away_team ? activeLiveMatch.away_team.name : 'Away'"></p>
                        <p class="text-4xl font-black text-emerald-400 mt-2" x-text="activeLiveMatch.away_score || 0"></p>
                    </div>
                </div>

                <!-- Score Controls -->
                <div class="flex items-center justify-center space-x-4 pt-4 border-t border-slate-800">
                    <button @click="activeLiveMatch.home_score++;" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold">+ Goal Home</button>
                    <button @click="activeLiveMatch.away_score++;" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold">+ Goal Away</button>
                    <button @click="finishMatch(activeLiveMatch)" class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-xs font-bold">End Match</button>
                </div>
            </div>
        </template>

        <template x-if="!activeLiveMatch">
            <div class="text-center py-12 text-xs text-slate-400 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                No active live match selected. Go to <button @click="activeTab = 'competitions'" class="text-emerald-600 underline font-bold">League Hub</button> and click <strong>Start</strong> on any fixture.
            </div>
        </template>
    </div>
</div>