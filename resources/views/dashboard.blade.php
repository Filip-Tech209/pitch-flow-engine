<!DOCTYPE html>
<html lang="en" 
      class="h-full" 
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }" 
      x-init="
          $watch('darkMode', val => {
              localStorage.setItem('theme', val ? 'dark' : 'light');
              if (val) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          });
          if (darkMode) document.documentElement.classList.add('dark');
      ">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Pitch Flow Engine</title>

    <!-- Tailwind CSS CDN Fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased font-sans" 
      x-data="dashboardApp()" 
      x-init="initDashboard()">

    <!-- TOP BAR / NAVIGATION HEADER -->
    <header class="w-full border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-28 py-2 flex items-center justify-between">
            
            <!-- Left: Brand Logo & Competition Context Switcher -->
            <div class="flex items-center space-x-4 shrink-0">
                <a href="/dashboard" class="flex items-center space-x-3">
                    <img src="{{ asset('logos/logo.png') }}" 
                        alt="Pitch Flow Logo" 
                        class="h-24 w-24 object-contain rounded-md shrink-0"
                        onerror="this.style.display='none'">
                    
                    <span class="text-lg font-black uppercase tracking-wider text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-blue-600">
                        Pitch Flow
                    </span>
                </a>

                <div class="h-12 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block"></div>

                <!-- Global Competition Context Switcher -->
                <div class="relative min-w-[180px] max-w-[260px]">
                    <select x-model="selectedCompetitionId" 
                            @change="onCompetitionChange()"
                            class="w-full px-3 py-2 pr-8 rounded-lg bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer truncate">
                        <option value="">-- Select Competition --</option>
                        <template x-for="comp in stats.competitions" :key="comp.id">
                            <option :value="comp.id" x-text="comp.name + ' (' + comp.code + ')'"></option>
                        </template>
                    </select>
                </div>
            </div>

            <!-- Right: Utilities & Controls -->
            <div class="flex items-center space-x-3">
                <a href="/" class="hidden sm:inline-flex items-center px-3.5 py-2 rounded-lg border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:border-emerald-500 dark:hover:border-emerald-500 hover:text-emerald-600 transition-all">
                    + New League Wizard
                </a>

                <!-- Theme Toggle Button -->
                <button @click="darkMode = !darkMode" 
                        type="button"
                        aria-label="Toggle Theme"
                        class="p-2.5 rounded-lg border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 hover:scale-105 active:scale-95 transition-all focus:outline-none">
                    <template x-if="darkMode">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </template>
                    <template x-if="!darkMode">
                        <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </template>
                </button>
            </div>
        </div>

        <!-- NAVIGATION TABS HUB -->
        <div class="border-t border-slate-100 dark:border-slate-800/60 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex space-x-1 sm:space-x-2 overflow-x-auto no-scrollbar">
                <template x-for="tab in tabs" :key="tab.id">
                    <button @click="activeTab = tab.id"
                            :class="activeTab === tab.id 
                                ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-500/5' 
                                : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 font-medium'"
                            class="py-3 px-3 sm:px-4 border-b-2 text-xs uppercase tracking-wider whitespace-nowrap transition-all flex items-center space-x-2 rounded-t-lg">
                        <span x-html="tab.icon"></span>
                        <span x-text="tab.label"></span>
                    </button>
                </template>
            </div>
        </div>
    </header>

    <!-- MAIN DASHBOARD CONTENT AREA -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 w-full">
        
        <!-- ==================== TAB 1: OVERVIEW ==================== -->
        <div x-show="activeTab === 'overview'" x-cloak class="space-y-6">
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:border-emerald-500/50">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Teams</p>
                    <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-2" x-text="stats.total_teams || 0"></p>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:border-emerald-500/50">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Players</p>
                    <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-2" x-text="stats.total_players || 0"></p>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:border-emerald-500/50">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Leagues</p>
                    <p class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-2" x-text="stats.active_leagues || 0"></p>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:border-emerald-500/50">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Engine Status</p>
                    <div class="flex items-center space-x-2 mt-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-300 tracking-wider">ACTIVE</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Upcoming / Recent Matches (Reactive to selected competition) -->
                <div class="lg:col-span-2 p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">
                            Fixtures & Match Logs
                        </h3>
                        <button @click="activeTab = 'matches'" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">View Live Console &rarr;</button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="match in leagueMatches.slice(0, 5)" :key="match.id">
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200/60 dark:border-slate-800/60 hover:border-emerald-500/30 transition-all">
                                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 w-1/3 truncate" x-text="match.home_team ? match.home_team.name : 'TBD'"></span>
                                <div class="text-center">
                                    <span class="px-2.5 py-1 rounded-full bg-slate-200 dark:bg-slate-800 text-[10px] font-bold tracking-widest uppercase text-slate-700 dark:text-slate-300" x-text="match.status"></span>
                                    <p class="text-[11px] font-black text-emerald-600 mt-1" x-show="match.status === 'FULL_TIME'" x-text="match.home_score + ' - ' + match.away_score"></p>
                                </div>
                                <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 w-1/3 text-right truncate" x-text="match.away_team ? match.away_team.name : 'TBD'"></span>
                            </div>
                        </template>

                        <div x-show="leagueMatches.length === 0" class="text-center py-10 text-xs text-slate-400 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                            No active matches found for this competition. Go to **League Hub** to generate fixtures.
                        </div>
                    </div>
                </div>

                <!-- Golden Boot Quick View -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-3">
                        Top Scorers (Golden Boot)
                    </h3>

                    <div class="space-y-2">
                        <template x-for="(item, idx) in standingsData.golden_boot" :key="item.player_id">
                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800">
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200" x-text="(idx + 1) + '. ' + (item.player ? item.player.name : 'Unknown')"></span>
                                <span class="text-xs font-black text-amber-500" x-text="item.goals + ' G'"></span>
                            </div>
                        </template>

                        <div x-show="!standingsData.golden_boot || standingsData.golden_boot.length === 0" class="text-center py-12 text-xs text-slate-400 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-xl">
                            No goal records logged yet.
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- ==================== TAB 2: LEAGUE MANAGEMENT ==================== -->
        @include('pannels.leaguehub')

        <!-- ==================== TAB 3: MATCHES ==================== -->
        @include('pannels.livematch')

        <!-- ==================== TAB 4: TEAMS ==================== -->
        <div x-show="activeTab === 'teams'" x-cloak class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                Team Registrations
            </h2>
            <p class="text-xs text-slate-500">Register new clubs, upload logos, and manage team home venues.</p>
        </div>

        <!-- ==================== TAB 5: PLAYERS ==================== -->
        <div x-show="activeTab === 'players'" x-cloak class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                Player Registrations & Roster
            </h2>
            <p class="text-xs text-slate-500">Manage player profiles, assign jersey numbers, and register squad members.</p>
        </div>

        <!-- ==================== TAB 6: STANDINGS ==================== -->
        @include('pannels.standings')

    </main>

    <!-- FOOTER -->
    <footer class="w-full py-4 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-center text-xs font-bold text-slate-500 dark:text-slate-400 tracking-wide">
        Made by DrD Solutions Ltd | 0701951531
    </footer>

    <!-- APP CONTROLLER SCRIPT -->
    <script>
        function dashboardApp() {
            return {
                activeTab: 'overview',
                selectedCompetitionId: '',
                isGenerating: false,
                loadingStepMessage: '',
                leagueMatches: [],
                activeLiveMatch: null,
                standingsData: { standings: [], golden_boot: [] },
                stats: @json($globalData),

                // Helper: Format Date
                formatMatchDate(datetimeStr) {
                    if (!datetimeStr) return { day: '12', month: 'OCT' };
                    const dt = new Date(datetimeStr);
                    return {
                        day: dt.getDate(),
                        month: dt.toLocaleString('default', { month: 'short' }).toUpperCase()
                    };
                },

                // Helper: 1.5 Hours Window
                formatMatchTimeWindow(datetimeStr) {
                    if (!datetimeStr) return '14:00 - 15:30';
                    const start = new Date(datetimeStr);
                    const end = new Date(start.getTime() + (90 * 60 * 1000));
                    const formatTime = (d) => d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
                    return `${formatTime(start)} - ${formatTime(end)}`;
                },
                
                // Defined Tabs array so navigation buttons render properly
                tabs: [
                    { id: 'overview', label: 'Overview', icon: '📊' },
                    { id: 'league', label: 'League Hub', icon: '⚙️' },
                    { id: 'matches', label: 'Live Console', icon: '⚽' },
                    { id: 'teams', label: 'Teams', icon: '🛡️' },
                    { id: 'players', label: 'Players', icon: '🏃' },
                    { id: 'standings', label: 'Standings', icon: '🏆' }
                ],

                async initDashboard() {
                    if (this.stats && this.stats.competitions && this.stats.competitions.length > 0) {
                        this.selectedCompetitionId = this.stats.competitions[0].id;
                        await this.onCompetitionChange();
                    }
                },

                async onCompetitionChange() {
                    if (!this.selectedCompetitionId) return;
                    await Promise.all([
                        this.fetchFixtures(),
                        this.fetchStandings()
                    ]);
                },

                async fetchFixtures() {
                    if (!this.selectedCompetitionId) return;
                    try {
                        const res = await fetch(`/api/dashboard/fixtures?competition_id=${this.selectedCompetitionId}`);
                        if (res.ok) {
                            const data = await res.json();
                            this.leagueMatches = data.matches || [];
                        }
                    } catch (e) {
                        console.error('Failed to load fixtures:', e);
                    }
                },

                async fetchStandings() {
                    if (!this.selectedCompetitionId) return;
                    try {
                        const res = await fetch(`/api/dashboard/standings?competition_id=${this.selectedCompetitionId}`);
                        if (res.ok) {
                            this.standingsData = await res.json();
                        }
                    } catch (e) {
                        console.error('Failed to load standings:', e);
                    }
                },

                // Updated & Safe generateFixtures Method
                async generateFixtures() {
                    if (!this.selectedCompetitionId) {
                        alert('Please select a competition from the switcher dropdown first.');
                        return;
                    }

                    this.isGenerating = true;
                    this.loadingStepMessage = "Fetching registered teams...";

                    const step1 = setTimeout(() => {
                        if (this.isGenerating) this.loadingStepMessage = "Applying round-robin algorithm...";
                    }, 600);

                    const step2 = setTimeout(() => {
                        if (this.isGenerating) this.loadingStepMessage = "Allocating 1.5-hour time windows & venues...";
                    }, 1200);

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                        
                        const res = await fetch('/api/dashboard/generate-fixtures', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ competition_id: this.selectedCompetitionId })
                        });

                        if (res.ok) {
                            this.loadingStepMessage = "Saving schedule to database...";
                            await this.onCompetitionChange();
                        } else {
                            const err = await res.json();
                            alert(err.message || err.error || 'Failed to generate fixtures. Ensure at least 2 teams belong to this season.');
                        }
                    } catch (e) {
                        console.error('Error generating fixtures:', e);
                        alert('Network request failed. Please check browser console or endpoint route.');
                    } finally {
                        clearTimeout(step1);
                        clearTimeout(step2);
                        this.isGenerating = false;
                        this.loadingStepMessage = '';
                    }
                },

                async startMatch(match) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const res = await fetch(`/api/dashboard/matches/${match.id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ status: 'LIVE_1ST_HALF' })
                    });
                    if (res.ok) {
                        match.status = 'LIVE_1ST_HALF';
                        this.activeLiveMatch = match;
                        this.activeTab = 'matches';
                    }
                },

                async recordGoal(match, teamId, playerId) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const res = await fetch(`/api/dashboard/matches/${match.id}/events`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            team_id: teamId,
                            player_id: playerId,
                            type: 'GOAL',
                            minute: 45
                        })
                    });
                    if (res.ok) {
                        await this.onCompetitionChange();
                    }
                }
            };
        }
    </script>
</body>
</html>