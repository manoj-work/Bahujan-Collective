<div>
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: flex;">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeModal"></div>

            <!-- Modal -->
            <div class="relative z-50 w-full max-w-md rounded-2xl border border-slate-800/70 bg-slate-900/90 p-6 shadow-2xl backdrop-blur sm:p-8">
                @if ($submitted)
                    <div class="text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/20">
                            <svg class="h-8 w-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-semibold text-white">Thank You!</h3>
                        <p class="text-slate-300">Your feedback has been submitted successfully.</p>
                        <button wire:click="closeModal" class="mt-6 rounded-full bg-slate-800/70 px-6 py-2 font-medium text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/70">
                            Close
                        </button>
                    </div>
                @else
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-semibold text-white">Send Feedback</h3>
                            <button wire:click="closeModal" class="rounded-lg p-1 text-slate-400 transition hover:bg-slate-800/50 hover:text-white">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-slate-400">We'd love to hear your thoughts and suggestions.</p>
                    </div>

                    <form wire:submit="submit" class="space-y-4">
                        <div>
                            <label for="feedback-name" class="mb-2 block text-sm font-medium text-slate-300">Name <span class="text-red-400">*</span></label>
                            <input type="text" id="feedback-name" wire:model="name" required
                                class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                placeholder="Your name">
                            @error('name') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="feedback-email" class="mb-2 block text-sm font-medium text-slate-300">Email <span class="text-red-400">*</span></label>
                            <input type="email" id="feedback-email" wire:model="email" required
                                class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                placeholder="your.email@example.com">
                            @error('email') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="feedback-message" class="mb-2 block text-sm font-medium text-slate-300">Message <span class="text-red-400">*</span></label>
                            <textarea id="feedback-message" wire:model="message" required rows="5"
                                class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                placeholder="Share your feedback, suggestions, or report any issues..."></textarea>
                            @error('message') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button type="button" wire:click="closeModal" class="rounded-full bg-slate-800/70 px-6 py-2 font-medium text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/70">
                                Cancel
                            </button>
                            <button type="submit" class="rounded-full bg-indigo-500 px-6 py-2 font-semibold text-white shadow-md shadow-indigo-500/40 transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/30">
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('feedback-submitted', () => {
                setTimeout(() => {
                    @this.closeModal();
                }, 2000);
            });
        });
    </script>
</div>

