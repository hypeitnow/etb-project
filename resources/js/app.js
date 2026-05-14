import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;

window.adminUserSearch = function adminUserSearch(searchUrl) {
    return {
        query: '',
        results: [],
        highlightedId: null,
        async search() {
            if (this.query.trim().length < 2) {
                this.results = [];
                return;
            }

            const response = await fetch(`${searchUrl}?q=${encodeURIComponent(this.query)}`, {
                headers: { Accept: 'application/json' },
            });

            this.results = await response.json();
        },
        focusUser(user) {
            this.results = [];
            this.query = user.name;
            this.highlightedId = user.id;

            const element = document.getElementById(`managed-user-${user.id}`);
            if (!element) {
                window.location.href = `${window.location.pathname}?page=${user.page}&focus_user=${user.id}#managed-user-${user.id}`;
                return;
            }

            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            element.classList.add('admin-highlight');

            setTimeout(() => {
                element.classList.remove('admin-highlight');
            }, 4500);
        },
    };
};

document.addEventListener('DOMContentLoaded', () => {
    const focusUser = new URLSearchParams(window.location.search).get('focus_user');
    if (!focusUser) return;

    const element = document.getElementById(`managed-user-${focusUser}`);
    if (!element) return;

    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
    element.classList.add('admin-highlight');

    setTimeout(() => {
        element.classList.remove('admin-highlight');
    }, 4500);
});

window.matchForm = function matchForm(config) {
    return {
        status: config.status,
        includeInLzkosz: Boolean(config.includeInLzkosz ?? false),
        isTicketed: Boolean(config.isTicketed ?? false),
        locations: [],
        opponents: [],
        opponentLogo: config.opponentLogo,
        async loadLocations(query) {
            const response = await fetch(`${config.locationsUrl}?q=${encodeURIComponent(query)}`, {
                headers: { Accept: 'application/json' },
            });
            this.locations = await response.json();
        },
        async loadOpponents(query) {
            const response = await fetch(`${config.opponentsUrl}?q=${encodeURIComponent(query)}`, {
                headers: { Accept: 'application/json' },
            });
            this.opponents = await response.json();
        },
        selectLocation(location) {
            this.$root.querySelector('[name="location"]').value = location.name;
            this.locations = [];
        },
        selectOpponent(opponent) {
            this.$root.querySelector('[name="opponent_name"]').value = opponent.name;
            this.opponentLogo = opponent.logo_path ? `/storage/${opponent.logo_path}` : null;
            this.opponents = [];
        },
        syncTime(value) {
            const input = this.$root.querySelector('[name="match_date"]');
            if (!input || !value) return;

            const date = input.value ? input.value.slice(0, 10) : new Date().toISOString().slice(0, 10);
            input.value = `${date}T${value}`;
        },
    };
};

window.newsLightbox = function newsLightbox() {
    return {
        image: null,
        open(path) {
            this.image = path;
        },
        close() {
            this.image = null;
        },
    };
};

window.adminPanel = function adminPanel(config) {
    return {
        openModal: null,
        matchFilter: 'all',
        newsFilter: 'all',
        publishAction: null,
        panelSearch: '',
        notificationsOpen: false,
        accountOpen: false,
        previewNotification: null,
        unreadCount: Number(config.unreadCount || 0),
        currentAccount: config.currentAccount,
        savedAccounts: [],
        get notificationBadge() {
            return this.unreadCount > 99 ? '+99' : String(this.unreadCount);
        },
        init() {
            this.savedAccounts = this.readSavedAccounts();
            if (!this.savedAccounts.some((account) => account.email === this.currentAccount.email)) {
                this.savedAccounts.unshift(this.currentAccount);
                this.persistSavedAccounts();
            }
        },
        readSavedAccounts() {
            try {
                return JSON.parse(localStorage.getItem('etb.admin.accounts') || '[]');
            } catch {
                return [];
            }
        },
        persistSavedAccounts() {
            localStorage.setItem('etb.admin.accounts', JSON.stringify(this.savedAccounts.slice(0, 6)));
        },
        saveCurrentAccount() {
            this.savedAccounts = [
                this.currentAccount,
                ...this.savedAccounts.filter((account) => account.email !== this.currentAccount.email),
            ].slice(0, 6);
            this.persistSavedAccounts();
        },
        switchAccount(account) {
            const loginUrl = new URL('/login', window.location.origin);
            loginUrl.searchParams.set('email', account.email);
            window.location.href = loginUrl.toString();
        },
        searchPanel() {
            const query = this.panelSearch.trim().toLowerCase();
            document.querySelectorAll('[data-admin-search]').forEach((element) => {
                if (!query) {
                    element.classList.remove('admin-highlight');
                    return;
                }

                const text = element.textContent.toLowerCase();
                if (text.includes(query)) {
                    element.classList.add('admin-highlight');
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    element.classList.remove('admin-highlight');
                }
            });
        },
    };
};

Alpine.start();

window.reinitializeUi = function reinitializeUi() {
    createIcons({ icons });
};

document.addEventListener('DOMContentLoaded', () => {
    window.reinitializeUi();
});

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('countdown');
    if (!el) return;

    const matchDate = el.dataset.date;
    if (!matchDate) return;

    const target = new Date(matchDate).getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const diff = target - now;

        if (diff <= 0) {
            el.innerHTML = 'Mecz trwa 🔥';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((diff / (1000 * 60)) % 60);
        const seconds = Math.floor((diff / 1000) % 60);

        el.innerHTML = `<span>${days} dni</span> : <span>${hours} godz</span> : <span>${minutes} min</span> : <span>${seconds} sek</span>`;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
});

window.adjustFontSize = function adjustFontSize(change) {
    const root = document.documentElement;
    const current = parseFloat(getComputedStyle(root).fontSize);
    const next = Math.min(22, Math.max(14, current + change * 16));
    root.style.fontSize = `${next}px`;
};

const searchIndex = [
    { label: 'Aktualności', url: '/news', keywords: ['aktualnosci', 'news'] },
    { label: 'Klub', url: '/club', keywords: ['klub'] },
    { label: 'Rozgrywki', url: '/schedule', keywords: ['rozgrywki', 'liga', 'terminarz'] },
    { label: 'Kontakt', url: '/contact', keywords: ['kontakt', 'email', 'telefon'] },
    { label: 'Drużyna', url: '/team', keywords: ['druzyna', 'zawodnicy'] },
    { label: 'Zawodnicy 3x3', url: '/team-3x3/players', keywords: ['3x3', 'trzy na trzy', 'zawodnicy 3x3', 'druzyna 3x3'] },
    { label: 'Tabela', url: '/schedule/table', keywords: ['tabela'] },
    { label: 'Terminarz ŁZKosz', url: '/schedule/lzkosz', keywords: ['łzkosz', 'lzkosz', 'terminarz łzkosz'] },
    { label: 'III liga mężczyzn ŁZKosz', url: '/schedule/third-league', keywords: ['iii liga', '3 liga', 'elkosz', 'lzkosz'] },
    { label: 'Partnerzy', url: '/#partners', keywords: ['sponsorzy', 'sponsor', 'partner', 'partnerzy', 'partner strategiczny', 'partner technologiczny'] },
];

function populateSearchSuggestions() {
    const datalist = document.getElementById('etb-search-suggestions');
    if (!datalist) return;

    datalist.innerHTML = searchIndex
        .map((item) => `<option value="${item.label}"></option>`)
        .join('');
}

document.addEventListener('DOMContentLoaded', populateSearchSuggestions);

window.etbSearch = function etbSearch() {
    const input = document.getElementById('etb-search');
    if (!input) return;

    const query = input.value.trim().toLowerCase();
    if (!query) return;

    const directMatch = searchIndex.find((item) => item.label.toLowerCase() === query);
    const partialMatch = searchIndex.find((item) => item.label.toLowerCase().includes(query)
        || item.keywords.some((keyword) => keyword.includes(query) || query.includes(keyword)));

    const result = directMatch || partialMatch;

    if (result) {
        window.location.href = result.url;
        return;
    }

    const main = document.getElementById('app-main');
    if (main && main.innerText.toLowerCase().includes(query)) {
        window.find(input.value);
        return;
    }

    alert('Brak wyników dla podanej frazy.');
};

window.materialsCarousel = function materialsCarousel(items) {
    return {
        items,
        page: 0,
        timer: null,
        get chunks() {
            const result = [];
            for (let i = 0; i < this.items.length; i += 4) {
                result.push(this.items.slice(i, i + 4));
            }
            return result;
        },
        get visibleItems() {
            return this.chunks[this.page] ?? [];
        },
        goTo(index) {
            this.page = index;
        },
        start() {
            if (this.chunks.length <= 1) return;
            this.timer = setInterval(() => {
                this.page = (this.page + 1) % this.chunks.length;
            }, 5000);
        },
    };
};
