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
    <title>Pitch Flow Engine</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full flex flex-col justify-between bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased" x-data="welcomeApp()" x-init="fetchTeams()">

    <!-- Header / Theme Switcher -->
    <header class="w-full max-w-3xl mx-auto px-6 py-6 flex items-center justify-between">
        <div class="text-xs font-semibold uppercase tracking-widest text-slate-400 dark:text-slate-500">
            Pitch Flow Engine | v1.0.0
        </div>

        <button @click="darkMode = !darkMode" 
                type="button"
                class="p-3 rounded-full border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 hover:scale-105 shadow-sm transition-all focus:outline-none">
            
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
    </header>

    <!-- Main Content -->
    <main class="w-full max-w-xl mx-auto px-6 py-4 flex-1 flex flex-col items-center justify-center text-center">
        
        <!-- Logo Header -->
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('logos/logo.png') }}" alt="Pitch Flow Engine Logo" class="h-28 w-auto mb-4 object-contain">
            
            <p class="text-xs sm:text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-blue-600 uppercase tracking-widest mt-1">
                Automated Football Manager
            </p>
        </div>

        <!-- LANDING STATE -->
        <div x-show="!started" class="w-full space-y-6">
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm text-center">
                <p class="text-xs sm:text-lg text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    League scheduling, Real-Time Match Events Logging, Team & Squad registrations, Automated standing tables, Automated Golden Boot tracking and many more.
                </p>
            </div>

            <div>
                <button @click="started = true" 
                        class="px-10 py-3.5 rounded-full bg-gradient-to-r from-green-600 via-emerald-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-bold text-xs uppercase tracking-widest shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    Get Started
                </button>
            </div>
        </div>

        <!-- WIZARD CARD STATE -->
        <div x-show="started" class="w-full" x-cloak>
            
            <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm text-left relative overflow-hidden">

                <!-- Header Banner -->
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3 mb-6">
                    <span class="text-xs font-semibold uppercase text-emerald-600 dark:text-emerald-400 tracking-wider">
                        Step <span x-text="step"></span> of 3: 
                        <span x-text="step === 1 ? 'Basic Details' : (step === 2 ? 'Format & Schedule' : 'Select Teams')"></span>
                    </span>
                    <button x-show="step < 4 && !loading" @click="resetWizard()" class="text-xs font-medium text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        Cancel
                    </button>
                </div>

                <!-- SIMULATED LOADER -->
                <div x-show="loading" class="py-12 flex flex-col items-center justify-center space-y-4">
                    <svg class="animate-spin h-8 w-8 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 animate-pulse" x-text="loadingText"></p>
                </div>

                <!-- WIZARD FORM CONTENT -->
                <form x-show="!loading" @submit.prevent="handleFormSubmit()" class="space-y-4">
                    
                    <!-- STEP 1: Name & Short Code -->
                    <template x-if="step === 1">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Competition Name</label>
                                <input type="text" x-model="form.name" required placeholder="PITCH FLOW PREMIER LEAGUE" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-sm focus:outline-none focus:border-emerald-600">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Short Code</label>
                                <input type="text" x-model="form.code" required placeholder="PFPL" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-sm focus:outline-none focus:border-emerald-600 uppercase">
                            </div>

                            <div class="pt-4 flex justify-end">
                                <button type="button" @click="nextStep(2, 'Loading format configurations...')" class="px-6 py-3 rounded-full bg-gradient-to-r from-green-600 via-emerald-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white text-xs font-bold uppercase tracking-wider transition-all shadow-sm">
                                    Next: Format & Dates &rarr;
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- STEP 2: Type, Start & End Date -->
                    <template x-if="step === 2">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Competition Type</label>
                                <select x-model="form.type" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-sm focus:outline-none focus:border-emerald-600">
                                    <option value="LEAGUE">LEAGUE</option>
                                    <option value="KNOCKOUT">KNOCKOUT</option>
                                    <option value="HYBRID">HYBRID</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Start Date</label>
                                    <input type="date" x-model="form.start_date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-sm focus:outline-none focus:border-emerald-600">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">End Date</label>
                                    <input type="date" x-model="form.end_date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-sm focus:outline-none focus:border-emerald-600">
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4">
                                <button type="button" @click="step = 1" class="px-5 py-2.5 rounded-full border border-slate-300 dark:border-slate-700 text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                                    Back
                                </button>
                                <button type="button" @click="nextStep(3, 'Fetching registered teams...')" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-green-600 via-emerald-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white text-xs font-bold uppercase tracking-wider transition-colors shadow-sm">
                                    Next: Select Teams &rarr;
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- STEP 3: Checkbox Options for All Registered Teams -->
                    <template x-if="step === 3">
                        <div class="space-y-4">
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400">
                                Select Registered Teams (<span x-text="form.team_ids.length"></span> selected)
                            </label>

                            <!-- Registered Teams Scroll Container -->
                            <div class="max-h-60 overflow-y-auto space-y-2 border border-slate-200 dark:border-slate-800 rounded-xl p-3 bg-slate-50/50 dark:bg-slate-950/50">
                                <template x-for="team in teams" :key="team.id">
                                    <label class="flex items-center space-x-3 p-3 rounded-lg border border-slate-200/60 dark:border-slate-800/60 bg-white dark:bg-slate-900 cursor-pointer hover:border-emerald-500 transition-all">
                                        <input type="checkbox" :value="team.id" x-model="form.team_ids" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                        <span class="text-xs font-medium text-slate-800 dark:text-slate-200" x-text="team.name"></span>
                                    </label>
                                </template>

                                <div x-show="teams.length === 0" class="text-center py-4 text-xs text-slate-400">
                                    No registered teams found in the database.
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4">
                                <button type="button" @click="step = 2" class="px-5 py-2.5 rounded-full border border-slate-300 dark:border-slate-700 text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                                    Back
                                </button>
                                <button type="submit" class="px-6 py-2.5 rounded-full bg-gradient-to-r from-green-600 via-emerald-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white text-xs font-bold uppercase tracking-wider transition-colors shadow-sm">
                                    Create Competition
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- FINAL SCREEN: Redirect & Success -->
                    <template x-if="step === 4">
                        <div class="text-center py-6 space-y-4">
                            <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Created Successfully!</h2>
                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400">
                                Your <span class="capitalize" x-text="form.type.toLowerCase()"></span> competition is live with <strong class="text-emerald-600 dark:text-emerald-400" x-text="form.team_ids.length"></strong> participating teams registered to season.
                            </p>

                            <div class="pt-4">
                                <button type="button" @click="goToDashboard()" class="w-full py-3 rounded-full bg-gradient-to-r from-green-600 via-emerald-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-bold text-xs uppercase tracking-widest shadow-md transition-all">
                                    Proceed to Managing the <span x-text="form.type"></span>
                                </button>
                            </div>
                        </div>
                    </template>

                </form>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="w-full py-6 text-center text-md font-bold text-slate-500 dark:text-slate-400 tracking-wide">
        Made by DrD Solutions Ltd | 0701951531
    </footer>

    <script>
        function welcomeApp() {
            return {
                started: false,
                step: 1,
                loading: false,
                loadingText: 'Processing...',
                teams: [],
                form: {
                    name: '',
                    code: '',
                    type: 'LEAGUE',
                    start_date: '',
                    end_date: '',
                    team_ids: []
                },
                async fetchTeams() {
                    try {
                        const res = await fetch('/api/teams', {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            this.teams = await res.json();
                        }
                    } catch (e) {
                        console.error('Failed to load teams', e);
                    }
                },
                nextStep(targetStep, message) {
                    if (this.step === 1 && (!this.form.name || !this.form.code)) {
                        alert('Please fill in both Competition Name and Short Code.');
                        return;
                    }
                    if (this.step === 2 && (!this.form.start_date || !this.form.end_date)) {
                        alert('Please select both Start and End dates.');
                        return;
                    }
                    this.triggerLoader(message, () => {
                        this.step = targetStep;
                    });
                },
                triggerLoader(message, callback) {
                    this.loadingText = message;
                    this.loading = true;
                    setTimeout(() => {
                        this.loading = false;
                        callback();
                    }, 1500);
                },
                async handleFormSubmit() {
                    if (this.form.team_ids.length < 2) {
                        alert('Please select at least 2 participating teams.');
                        return;
                    }

                    this.triggerLoader('Initializing competition, season & team registrations...', async () => {
                        try {
                            const response = await fetch('/api/competitions/initialize', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify(this.form)
                            });

                            const res = await response.json();

                            if (response.ok && res.success) {
                                this.step = 4;
                            } else {
                                if (res.errors) {
                                    const firstKey = Object.keys(res.errors)[0];
                                    alert('Validation Failure: ' + res.errors[firstKey][0]);
                                } else {
                                    alert(res.message || 'Initialization failed.');
                                }
                            }
                        } catch (e) {
                            console.error('Submit execution error:', e);
                            alert('Communication error with backend server. Check browser console.');
                        }
                    });
                },
                goToDashboard() {
                    window.location.href = '/dashboard';
                },
                resetWizard() {
                    this.started = false;
                    this.step = 1;
                }
            }
        }
    </script>
</body>
</html>