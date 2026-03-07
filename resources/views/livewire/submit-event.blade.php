<div class="relative mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        @if ($submitted)
            <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-8 text-center backdrop-blur">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/20">
                    <svg class="h-8 w-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="mb-2 text-2xl font-semibold text-white">Event Submitted Successfully!</h2>
                <p class="text-slate-300">Your event has been submitted and is pending approval. An admin will review it shortly.</p>
                <a href="{{ route('home') }}" class="mt-6 inline-block rounded-full bg-slate-800/70 px-6 py-2 font-medium text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/70">
                    Back to Home
                </a>
            </div>
        @else
            <div class="rounded-2xl border border-slate-800/70 bg-slate-900/70 p-6 backdrop-blur shadow-xl shadow-slate-900/50 sm:p-8">
                <div class="mb-6">
                    <h1 class="text-3xl font-semibold text-white">Submit an Event</h1>
                    <p class="mt-2 text-slate-400">Share your community event with the Bahujan Collective. All submissions require admin approval.</p>
                </div>

                <form wire:submit="submit" class="space-y-8">
                    <!-- Basic Information -->
                    <section class="space-y-4">
                        <h2 class="text-lg font-semibold text-white">Basic Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="eventName" class="mb-2 block text-sm font-medium text-slate-300">Event Name <span class="text-red-400">*</span></label>
                                <input type="text" id="eventName" wire:model="eventName" required
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="Enter event name">
                                @error('eventName') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="tagline" class="mb-2 block text-sm font-medium text-slate-300">Tagline</label>
                                <input type="text" id="tagline" wire:model="tagline"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="A short tagline for your event">
                                @error('tagline') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="description" class="mb-2 block text-sm font-medium text-slate-300">Description</label>
                                <textarea id="description" wire:model="description" rows="4"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="Describe your event..."></textarea>
                                @error('description') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="eventLink" class="mb-2 block text-sm font-medium text-slate-300">Event Link</label>
                                <input type="url" id="eventLink" wire:model="eventLink"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="https://example.com/event">
                                @error('eventLink') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="coverPic" class="mb-2 block text-sm font-medium text-slate-300">Cover Image URL</label>
                                <input type="url" id="coverPic" wire:model="coverPic"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="https://example.com/image.jpg">
                                @error('coverPic') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Category and Type -->
                    <section class="space-y-4">
                        <h2 class="text-lg font-semibold text-white">Category & Type</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="eventCategoryId" class="mb-2 block text-sm font-medium text-slate-300">Category</label>
                                <select id="eventCategoryId" wire:model="eventCategoryId"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('eventCategoryId') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="eventTypeId" class="mb-2 block text-sm font-medium text-slate-300">Event Type</label>
                                <select id="eventTypeId" wire:model="eventTypeId"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="">Select event type</option>
                                    @foreach ($types as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('eventTypeId') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Date and Time -->
                    <section class="space-y-4">
                        <h2 class="text-lg font-semibold text-white">Date & Time</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="startDate" class="mb-2 block text-sm font-medium text-slate-300">Start Date</label>
                                <input type="date" id="startDate" wire:model="startDate"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('startDate') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="endDate" class="mb-2 block text-sm font-medium text-slate-300">End Date</label>
                                <input type="date" id="endDate" wire:model="endDate"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('endDate') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label for="startTime" class="mb-2 block text-sm font-medium text-slate-300">Start Time</label>
                                <input type="time" id="startTime" wire:model="startTime"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('startTime') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="startAmPm" class="mb-2 block text-sm font-medium text-slate-300">AM/PM</label>
                                <select id="startAmPm" wire:model="startAmPm"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>

                            <div>
                                <label for="timeZone" class="mb-2 block text-sm font-medium text-slate-300">Time Zone</label>
                                <input type="text" id="timeZone" wire:model="timeZone"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="Asia/Kolkata">
                                @error('timeZone') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="endTime" class="mb-2 block text-sm font-medium text-slate-300">End Time</label>
                                <input type="time" id="endTime" wire:model="endTime"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                @error('endTime') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="endAmPm" class="mb-2 block text-sm font-medium text-slate-300">AM/PM</label>
                                <select id="endAmPm" wire:model="endAmPm"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Location -->
                    <section class="space-y-4">
                        <h2 class="text-lg font-semibold text-white">Location</h2>
                        <div class="space-y-4">
                            <div>
                                <label for="locationName" class="mb-2 block text-sm font-medium text-slate-300">Location Name</label>
                                <input type="text" id="locationName" wire:model="locationName"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="e.g., Community Center, Online, etc.">
                                @error('locationName') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <label for="country" class="mb-2 block text-sm font-medium text-slate-300">Country</label>
                                    <input type="text" id="country" wire:model="country"
                                        class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                        placeholder="India">
                                    @error('country') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="stateProvince" class="mb-2 block text-sm font-medium text-slate-300">State/Province</label>
                                    <input type="text" id="stateProvince" wire:model="stateProvince"
                                        class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                        placeholder="Karnataka">
                                    @error('stateProvince') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="cityTown" class="mb-2 block text-sm font-medium text-slate-300">City/Town</label>
                                    <input type="text" id="cityTown" wire:model="cityTown"
                                        class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                        placeholder="Bangalore">
                                    @error('cityTown') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="addressLine1" class="mb-2 block text-sm font-medium text-slate-300">Address Line 1</label>
                                <input type="text" id="addressLine1" wire:model="addressLine1"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="Street address">
                                @error('addressLine1') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="addressLine2" class="mb-2 block text-sm font-medium text-slate-300">Address Line 2</label>
                                <input type="text" id="addressLine2" wire:model="addressLine2"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="Apartment, suite, etc. (optional)">
                                @error('addressLine2') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="landmark" class="mb-2 block text-sm font-medium text-slate-300">Landmark</label>
                                <input type="text" id="landmark" wire:model="landmark"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="Near Metro Station, etc.">
                                @error('landmark') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Cost -->
                    <section class="space-y-4">
                        <h2 class="text-lg font-semibold text-white">Cost</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="currency" class="mb-2 block text-sm font-medium text-slate-300">Currency</label>
                                <input type="text" id="currency" wire:model="currency"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="INR">
                                @error('currency') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="costDonation" class="mb-2 block text-sm font-medium text-slate-300">Cost/Donation</label>
                                <input type="text" id="costDonation" wire:model="costDonation"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white placeholder:text-slate-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    placeholder="₹0 or Free">
                                @error('costDonation') <span class="mt-1 text-sm text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </section>

                    <!-- Visibility & RSVP -->
                    <section class="space-y-4">
                        <h2 class="text-lg font-semibold text-white">Visibility & RSVP</h2>
                        <div class="grid gap-4 sm:grid-cols-3">
                            <div>
                                <label for="eventVisibility" class="mb-2 block text-sm font-medium text-slate-300">Event Visibility</label>
                                <select id="eventVisibility" wire:model="eventVisibility"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="Public">Public</option>
                                    <option value="Restricted">Restricted</option>
                                </select>
                            </div>

                            <div>
                                <label for="eventRsvp" class="mb-2 block text-sm font-medium text-slate-300">RSVP</label>
                                <select id="eventRsvp" wire:model="eventRsvp"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="Open">Open</option>
                                    <option value="Restricted">Restricted</option>
                                </select>
                            </div>

                            <div>
                                <label for="rsvpVisibility" class="mb-2 block text-sm font-medium text-slate-300">RSVP Visibility</label>
                                <select id="rsvpVisibility" wire:model="rsvpVisibility"
                                    class="w-full rounded-lg border border-slate-700/50 bg-slate-800/50 px-4 py-2 text-white focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="ATTENDEE_CHOICE">Attendee Choice</option>
                                    <option value="MANDATORY">Mandatory</option>
                                    <option value="HIDDEN">Hidden</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-4 pt-4">
                        <a href="{{ route('home') }}" class="rounded-full bg-slate-800/70 px-6 py-2 font-medium text-slate-200 ring-1 ring-slate-700/80 transition hover:-translate-y-0.5 hover:bg-slate-700/70">
                            Cancel
                        </a>
                        <button type="submit" class="rounded-full bg-indigo-500 px-6 py-2 font-semibold text-white shadow-md shadow-indigo-500/40 transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-indigo-500/30">
                            Submit Event
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

