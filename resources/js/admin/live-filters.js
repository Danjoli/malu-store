const formSelector = '[data-admin-live-filters]';
const statusSelector = '[data-admin-filter-status]';

let searchTimer;
let activeRequest;

const initializeForms = (root = document) => {
    root.querySelectorAll(formSelector).forEach((form) => {
        form.querySelector('[data-admin-filter-submit]')?.classList.add('hidden');
        form.querySelector(statusSelector)?.classList.remove('hidden');
    });
};

const filteredUrl = (form) => {
    const url = new URL(form.action, window.location.origin);

    new FormData(form).forEach((value, key) => {
        const normalized = String(value).trim();
        if (normalized !== '') url.searchParams.set(key, normalized);
    });

    return url;
};

const restoreFocus = (name, position) => {
    if (!name) return;

    const field = document.querySelector(`${formSelector} [name="${CSS.escape(name)}"]`);
    field?.focus({ preventScroll: true });

    if (typeof field?.setSelectionRange === 'function' && position !== null) {
        field.setSelectionRange(position, position);
    }
};

const renderUrl = async (url, { history = 'replace', focusName = null, cursor = null } = {}) => {
    activeRequest?.abort();
    activeRequest = new AbortController();

    const currentMain = document.querySelector('main');
    const currentForm = currentMain?.querySelector(formSelector);
    const status = currentForm?.querySelector(statusSelector);

    currentForm?.setAttribute('aria-busy', 'true');
    if (status) status.textContent = 'Atualizando resultados…';

    try {
        const response = await fetch(url, {
            headers: {
                Accept: 'text/html',
                'X-Requested-With': 'XMLHttpRequest',
            },
            signal: activeRequest.signal,
        });

        if (!response.ok || response.redirected) {
            window.location.assign(response.url || url);
            return;
        }

        const nextDocument = new DOMParser().parseFromString(await response.text(), 'text/html');
        const nextMain = nextDocument.querySelector('main');
        if (!currentMain || !nextMain) {
            window.location.assign(url);
            return;
        }

        window.Alpine?.destroyTree(currentMain);
        currentMain.innerHTML = nextMain.innerHTML;
        window.Alpine?.initTree(currentMain);
        document.title = nextDocument.title;

        if (history === 'push') window.history.pushState({}, '', url);
        if (history === 'replace') window.history.replaceState({}, '', url);

        initializeForms(currentMain);
        restoreFocus(focusName, cursor);

        const nextStatus = currentMain.querySelector(statusSelector);
        if (nextStatus) nextStatus.textContent = 'Resultados atualizados automaticamente';
    } catch (error) {
        if (error.name === 'AbortError') return;

        currentForm?.removeAttribute('aria-busy');
        if (status) status.textContent = 'Não foi possível atualizar. Tente novamente.';
    }
};

document.addEventListener('submit', (event) => {
    const form = event.target.closest(formSelector);
    if (!form) return;

    event.preventDefault();
    renderUrl(filteredUrl(form));
});

document.addEventListener('input', (event) => {
    const form = event.target.closest(formSelector);
    if (!form || !event.target.matches('input[type="search"], input[type="text"], input:not([type])')) return;

    window.clearTimeout(searchTimer);
    const cursor = event.target.selectionStart;
    searchTimer = window.setTimeout(() => renderUrl(filteredUrl(form), {
        focusName: event.target.name,
        cursor,
    }), 250);
});

document.addEventListener('change', (event) => {
    const form = event.target.closest(formSelector);
    if (!form || !event.target.matches('select, input[type="date"]')) return;

    renderUrl(filteredUrl(form), { focusName: event.target.name });
});

document.addEventListener('click', (event) => {
    const reset = event.target.closest('[data-admin-filter-reset]');
    if (reset) {
        event.preventDefault();
        renderUrl(new URL(reset.href), { focusName: 'q' });
        return;
    }

    const paginationLink = event.target.closest('main nav[role="navigation"] a[href]');
    if (!paginationLink || !document.querySelector(formSelector)) return;

    event.preventDefault();
    renderUrl(new URL(paginationLink.href), { history: 'push' });
});

window.addEventListener('popstate', () => {
    if (document.querySelector(formSelector)) {
        renderUrl(new URL(window.location.href), { history: 'none' });
    }
});

initializeForms();
