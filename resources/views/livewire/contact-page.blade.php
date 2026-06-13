<div class="relative mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-6">
        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Contact & Feedback</p>
        <h1 class="mt-1 text-3xl font-semibold text-white">Get in touch</h1>
        <p class="mt-3 max-w-2xl text-slate-300">
            Send feedback about the site, report an issue, or reach out about collaboration.
        </p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
        <section class="rounded-2xl border border-slate-800/70 bg-slate-900/70 p-6 backdrop-blur shadow-xl shadow-slate-900/50 sm:p-8">
            <div class="space-y-5 text-sm leading-relaxed text-slate-300">
                <div>
                    <h2 class="text-base font-semibold text-white">Contact information</h2>
                    <p class="mt-2">
                        For questions, suggestions, or collaboration ideas, email us directly.
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-white">Email</h3>
                    <p class="mt-2">
                        <a
                            href="mailto:contact@bahujancollective.test"
                            class="font-medium text-cyan-300 hover:text-cyan-200 hover:underline"
                        >
                            contact@bahujancollective.test
                        </a>
                    </p>
                </div>
            </div>
        </section>

        <section id="feedback" class="rounded-2xl border border-slate-800/70 bg-slate-900/70 p-6 backdrop-blur shadow-xl shadow-slate-900/50 sm:p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-white">Send feedback</h2>
                <p class="mt-2 text-sm text-slate-400">We'd love to hear your thoughts and suggestions.</p>
            </div>

            @if ($feedbackSubmitted)
                <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-sm text-emerald-100">
                    Thank you. Your feedback has been submitted successfully.
                </div>
            @endif

            <form wire:submit="submitFeedback" class="space-y-4">
                <div>
                    <label for="feedback-name" class="mb-2 block text-sm font-medium text-slate-300">Name <span class="text-red-400">*</span></label>
                    <input
                        type="text"
                        id="feedback-name"
                        wire:model="name"
                        required
                        class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        placeholder="Your name"
                    >
                    @error('name') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="feedback-email" class="mb-2 block text-sm font-medium text-slate-300">Email <span class="text-red-400">*</span></label>
                    <input
                        type="email"
                        id="feedback-email"
                        wire:model="email"
                        required
                        class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        placeholder="your.email@example.com"
                    >
                    @error('email') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="feedback-message" class="mb-2 block text-sm font-medium text-slate-300">Message <span class="text-red-400">*</span></label>
                    <textarea
                        id="feedback-message"
                        wire:model="message"
                        required
                        rows="5"
                        class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        placeholder="Share your feedback, suggestions, or report any issues..."
                    ></textarea>
                    @error('message') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-end pt-4">
                    <button type="submit" class="rounded-full bg-indigo-500 px-6 py-2 font-semibold text-white shadow-md shadow-indigo-500/40 transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/30">
                        Submit Feedback
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
