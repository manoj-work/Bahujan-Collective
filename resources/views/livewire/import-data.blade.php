<div class="relative mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-8">
        <p class="text-xs uppercase tracking-[0.2em] text-indigo-300">Data tools</p>
        <h1 class="mt-2 text-3xl font-semibold text-white">Import events & organizations</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-300">
            Upload a CSV file to add or update records. Existing events are matched by name + start date + start time.
            Organizations are matched by organization name.
        </p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <section class="rounded-2xl border border-slate-800/70 bg-slate-900/80 p-6 shadow-xl shadow-slate-950/40">
            <div class="mb-5 flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">Events</h2>
                    <p class="mt-1 text-sm text-slate-400">Required column: <span class="text-slate-200">EVENT_NAME</span> (or title / name).</p>
                </div>
                <a href="{{ route('import.template', 'events') }}" class="shrink-0 whitespace-nowrap rounded-full bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-200 ring-1 ring-slate-700 hover:bg-slate-700">
                    Download sample
                </a>
            </div>

            <form wire:submit="importEvents" class="space-y-4" novalidate>
                <div class="csv-dropzone {{ $errors->has('eventsFile') ? 'csv-dropzone-error' : '' }}">
                    <input
                        id="events-csv"
                        type="file"
                        accept=".csv,.txt,text/csv"
                        wire:model="eventsFile"
                        class="csv-dropzone-input"
                    >
                    <div class="pointer-events-none flex flex-col items-center text-center">
                        <span class="csv-dropzone-button">Choose CSV file</span>
                        <span class="mt-2 text-xs text-slate-400">CSV only, up to 4 MB</span>
                        <span wire:loading wire:target="eventsFile" class="mt-2 text-xs font-medium text-indigo-300">Uploading…</span>
                    </div>
                </div>

                @if ($eventsFile)
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-950/70 px-3 py-2 ring-1 ring-slate-800">
                        <p class="truncate text-sm text-slate-200">{{ $eventsFile->getClientOriginalName() }}</p>
                        <button type="button" wire:click="clearEventsFile" class="text-xs font-semibold text-slate-400 hover:text-white">
                            Remove
                        </button>
                    </div>
                @endif

                @error('eventsFile')
                    <p class="text-sm font-medium text-red-400">{{ $message }}</p>
                @enderror

                <button
                    type="submit"
                    class="rounded-full bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-400 disabled:cursor-not-allowed disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:target="importEvents,eventsFile"
                >
                    <span wire:loading.remove wire:target="importEvents">Import events</span>
                    <span wire:loading wire:target="importEvents">Importing...</span>
                </button>
            </form>

            @if ($eventResult)
                <div class="mt-5 rounded-xl bg-slate-950/60 p-4 text-sm ring-1 ring-slate-800">
                    <p class="font-semibold text-emerald-300">Added {{ $eventResult['imported'] }}, updated {{ $eventResult['updated'] }}, skipped {{ $eventResult['skipped'] }}</p>
                    @if (! empty($eventResult['errors']))
                        <ul class="mt-3 space-y-1 text-xs text-rose-300">
                            @foreach (array_slice($eventResult['errors'], 0, 12) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-800/70 bg-slate-900/80 p-6 shadow-xl shadow-slate-950/40">
            <div class="mb-5 flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">Organizations</h2>
                    <p class="mt-1 text-sm text-slate-400">Required column: <span class="text-slate-200">ORG_NAME</span> (or organization / name).</p>
                </div>
                <a href="{{ route('import.template', 'organizations') }}" class="shrink-0 whitespace-nowrap rounded-full bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-200 ring-1 ring-slate-700 hover:bg-slate-700">
                    Download sample
                </a>
            </div>

            <form wire:submit="importOrganizations" class="space-y-4" novalidate>
                <div class="csv-dropzone {{ $errors->has('organizationsFile') ? 'csv-dropzone-error' : '' }}">
                    <input
                        id="organizations-csv"
                        type="file"
                        accept=".csv,.txt,text/csv"
                        wire:model="organizationsFile"
                        class="csv-dropzone-input"
                    >
                    <div class="pointer-events-none flex flex-col items-center text-center">
                        <span class="csv-dropzone-button csv-dropzone-button-cyan">Choose CSV file</span>
                        <span class="mt-2 text-xs text-slate-400">CSV only, up to 4 MB</span>
                        <span wire:loading wire:target="organizationsFile" class="mt-2 text-xs font-medium text-cyan-300">Uploading…</span>
                    </div>
                </div>

                @if ($organizationsFile)
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-950/70 px-3 py-2 ring-1 ring-slate-800">
                        <p class="truncate text-sm text-slate-200">{{ $organizationsFile->getClientOriginalName() }}</p>
                        <button type="button" wire:click="clearOrganizationsFile" class="text-xs font-semibold text-slate-400 hover:text-white">
                            Remove
                        </button>
                    </div>
                @endif

                @error('organizationsFile')
                    <p class="text-sm font-medium text-red-400">{{ $message }}</p>
                @enderror

                <button
                    type="submit"
                    class="rounded-full bg-cyan-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-cyan-500/20 transition hover:bg-cyan-400 disabled:cursor-not-allowed disabled:opacity-50"
                    wire:loading.attr="disabled"
                    wire:target="importOrganizations,organizationsFile"
                >
                    <span wire:loading.remove wire:target="importOrganizations">Import organizations</span>
                    <span wire:loading wire:target="importOrganizations">Importing...</span>
                </button>
            </form>

            @if ($organizationResult)
                <div class="mt-5 rounded-xl bg-slate-950/60 p-4 text-sm ring-1 ring-slate-800">
                    <p class="font-semibold text-emerald-300">Added {{ $organizationResult['imported'] }}, updated {{ $organizationResult['updated'] }}, skipped {{ $organizationResult['skipped'] }}</p>
                    @if (! empty($organizationResult['errors']))
                        <ul class="mt-3 space-y-1 text-xs text-rose-300">
                            @foreach (array_slice($organizationResult['errors'], 0, 12) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        </section>
    </div>
</div>
