let debounceTimer = null;
let activeIndex = -1;
let currentPlaces = [];
let bound = false;

function root() {
    return document.getElementById('location-search-root');
}

function input() {
    return document.getElementById('location-search');
}

function menu() {
    return document.getElementById('location-search-results');
}

function homeComponent() {
    const componentId = root()?.dataset.componentId;
    return componentId ? window.Livewire?.find(componentId) : null;
}

function applyLocation(value) {
    const field = input();
    if (field) {
        field.value = value;
    }

    hideMenu();
    homeComponent()?.set('location', value);
}

function hideMenu() {
    const results = menu();
    if (!results) {
        return;
    }

    results.innerHTML = '';
    results.classList.add('hidden');
    activeIndex = -1;
    currentPlaces = [];
}

function renderPlaces(places) {
    const results = menu();
    if (!results) {
        return;
    }

    currentPlaces = places;
    activeIndex = -1;

    if (places.length === 0) {
        results.innerHTML = '<li class="px-3 py-2 text-sm text-slate-400">No matching places</li>';
        results.classList.remove('hidden');
        return;
    }

    results.innerHTML = places
        .map(
            (place, index) =>
                `<li>
                    <button type="button" data-index="${index}" class="flex w-full items-start gap-2 px-3 py-2 text-left text-sm text-slate-100 hover:bg-slate-800">
                        <span class="mt-0.5 text-cyan-300">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M12 21s7-5 7-11a7 7 0 1 0-14 0c0 6 7 11 7 11Z" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="12" cy="10" r="2.5" />
                            </svg>
                        </span>
                        <span>${escapeHtml(place.label)}</span>
                    </button>
                </li>`
        )
        .join('');
    results.classList.remove('hidden');
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;');
}

function highlight(index) {
    const buttons = menu()?.querySelectorAll('button[data-index]') ?? [];
    buttons.forEach((button, buttonIndex) => {
        button.classList.toggle('bg-slate-800', buttonIndex === index);
    });
}

async function fetchPlaces(query) {
    if (query.trim().length < 2) {
        hideMenu();
        return;
    }

    try {
        const response = await fetch(`/places?q=${encodeURIComponent(query.trim())}`, {
            headers: { Accept: 'application/json' },
        });
        if (!response.ok) {
            hideMenu();
            return;
        }

        const payload = await response.json();
        renderPlaces(Array.isArray(payload.data) ? payload.data : []);
    } catch (error) {
        hideMenu();
    }
}

function bindLocationSearch() {
    if (bound) {
        return;
    }

    bound = true;

    document.addEventListener('input', (event) => {
        if (event.target?.id !== 'location-search') {
            return;
        }

        window.clearTimeout(debounceTimer);
        const value = event.target.value.trim();

        if (value === '') {
            hideMenu();
            applyLocation('');
            return;
        }

        debounceTimer = window.setTimeout(() => {
            fetchPlaces(value);
        }, 280);
    });

    document.addEventListener('search', (event) => {
        if (event.target?.id !== 'location-search') {
            return;
        }

        if (event.target.value.trim() === '') {
            hideMenu();
            applyLocation('');
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.target?.id !== 'location-search') {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (currentPlaces.length === 0) {
                return;
            }
            activeIndex = (activeIndex + 1) % currentPlaces.length;
            highlight(activeIndex);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (currentPlaces.length === 0) {
                return;
            }
            activeIndex = activeIndex <= 0 ? currentPlaces.length - 1 : activeIndex - 1;
            highlight(activeIndex);
        } else if (event.key === 'Enter') {
            event.preventDefault();
            if (activeIndex >= 0 && currentPlaces[activeIndex]) {
                applyLocation(currentPlaces[activeIndex].value);
            } else {
                applyLocation(event.target.value.trim());
            }
        } else if (event.key === 'Escape') {
            hideMenu();
        }
    });

    document.addEventListener('click', (event) => {
        const button = event.target.closest('#location-search-results button[data-index]');
        if (button) {
            const place = currentPlaces[Number(button.dataset.index)];
            if (place) {
                applyLocation(place.value);
            }
            return;
        }

        if (!root()?.contains(event.target)) {
            hideMenu();
        }
    });
}

document.addEventListener('livewire:init', bindLocationSearch);
document.addEventListener('livewire:navigated', bindLocationSearch);
