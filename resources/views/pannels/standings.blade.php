<div x-show="activeTab === 'standings'" x-cloak class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- League Standings Table -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                League Table Standings
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase font-black">
                            <th class="py-3 px-2">#</th>
                            <th class="py-3 px-2">Club</th>
                            <th class="py-3 px-2 text-center">MP</th>
                            <th class="py-3 px-2 text-center">W</th>
                            <th class="py-3 px-2 text-center">D</th>
                            <th class="py-3 px-2 text-center">L</th>
                            <th class="py-3 px-2 text-center">GF</th>
                            <th class="py-3 px-2 text-center">GA</th>
                            <th class="py-3 px-2 text-center">GD</th>
                            <th class="py-3 px-2 text-center font-bold text-emerald-600">Pts</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-semibold text-slate-800 dark:text-slate-200">
                        <template x-for="(row, idx) in standingsData.standings" :key="row.team_id">
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-950 transition-colors">
                                <td class="py-3 px-2 font-black text-slate-400" x-text="idx + 1"></td>
                                <td class="py-3 px-2 font-bold" x-text="row.name"></td>
                                <td class="py-3 px-2 text-center" x-text="row.played"></td>
                                <td class="py-3 px-2 text-center text-emerald-600" x-text="row.won"></td>
                                <td class="py-3 px-2 text-center text-amber-600" x-text="row.drawn"></td>
                                <td class="py-3 px-2 text-center text-red-500" x-text="row.lost"></td>
                                <td class="py-3 px-2 text-center" x-text="row.gf"></td>
                                <td class="py-3 px-2 text-center" x-text="row.ga"></td>
                                <td class="py-3 px-2 text-center font-bold" x-text="row.gd > 0 ? '+' + row.gd : row.gd"></td>
                                <td class="py-3 px-2 text-center font-black text-emerald-600 text-sm" x-text="row.points"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Golden Boot Leaderboard Widget -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-amber-500 flex items-center space-x-2">
                <span>🏆 Golden Boot Race</span>
            </h2>

            <div class="space-y-3">
                <template x-for="(item, idx) in standingsData.golden_boot" :key="item.player_id">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800">
                        <div class="flex items-center space-x-3">
                            <span class="text-xs font-black text-slate-400" x-text="'#' + (idx + 1)"></span>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="item.player ? item.player.name : 'Unknown Player'"></p>
                                <p class="text-[10px] text-slate-400" x-text="item.player && item.player.team ? item.player.team.name : 'N/A'"></p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-600 font-black text-xs" x-text="item.goals + ' Goals'"></span>
                    </div>
                </template>
            </div>
        </div>

    </div>
</div>