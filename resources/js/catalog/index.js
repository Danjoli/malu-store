const shellSelector = '[data-catalog-shell]';
let requestController;
let searchTimer;

const setLoading = (loading) => {
    const shell = document.querySelector(shellSelector);
    if (!shell) return;

    shell.setAttribute('aria-busy', String(loading));
    shell.classList.toggle('opacity-60', loading);
    shell.classList.toggle('pointer-events-none', loading);
};

const loadCatalog = async (url, pushState = true, keepSearchFocus = false) => {
    const filtersWereOpen = window.matchMedia('(max-width: 1023px)').matches
        && !document.querySelector('[data-catalog-filters]')?.classList.contains('hidden');

    requestController?.abort();
    const controller = new AbortController();
    requestController = controller;
    setLoading(true);

    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: controller.signal,
        });

        if (!response.ok) throw new Error('Não foi possível atualizar o catálogo.');

        const documentResult = new DOMParser().parseFromString(await response.text(), 'text/html');
        const nextShell = documentResult.querySelector(shellSelector);
        const currentShell = document.querySelector(shellSelector);
        if (!nextShell || !currentShell) throw new Error('Resposta do catálogo inválida.');

        currentShell.replaceWith(nextShell);
        if (filtersWereOpen) {
            document.querySelector('[data-catalog-filters]')?.classList.remove('hidden');
            document.querySelector('[data-catalog-filter-toggle]')?.setAttribute('aria-expanded', 'true');
        }
        document.title = documentResult.title;
        if (pushState) window.history.pushState({}, '', url);
        if (keepSearchFocus) {
            const search = document.querySelector(`${shellSelector} [name="search"]`);
            search?.focus({ preventScroll: true });
            search?.setSelectionRange(search.value.length, search.value.length);
        } else {
            document.querySelector('[data-catalog-heading]')?.focus({ preventScroll: true });
        }
    } catch (error) {
        if (error.name !== 'AbortError') window.location.assign(url);
    } finally {
        if (requestController === controller) setLoading(false);
    }
};

const submitForm = (form, keepSearchFocus = false) => {
    const data = new FormData(form);
    const params = new URLSearchParams();

    data.forEach((value, key) => {
        if (String(value).trim() !== '' && !(key === 'sort' && value === 'recent')) params.append(key, value);
    });

    loadCatalog(`${form.action}${params.size ? `?${params}` : ''}`, true, keepSearchFocus);
};

document.addEventListener('submit', (event) => {
    const form = event.target.closest('[data-catalog-form]');
    if (!form) return;
    event.preventDefault();
    submitForm(form);
});

document.addEventListener('change', (event) => {
    const form = event.target.closest('[data-catalog-form]');
    if (!form || !event.target.matches('select, input[type="checkbox"], input[type="radio"]')) return;
    submitForm(form);
});

document.addEventListener('input', (event) => {
    const form = event.target.closest('[data-catalog-form]');
    if (!form || !event.target.matches('[data-catalog-live-input]')) return;

    requestController?.abort();
    const keepSearchFocus = event.target.matches('[name="search"]');
    window.clearTimeout(searchTimer);
    searchTimer = window.setTimeout(() => submitForm(form, keepSearchFocus), 450);
});

document.addEventListener('click', (event) => {
    const link = event.target.closest('[data-catalog-link]');
    if (link && !event.ctrlKey && !event.metaKey && !event.shiftKey) {
        event.preventDefault();
        loadCatalog(link.href);
        return;
    }

    const toggle = event.target.closest('[data-catalog-filter-toggle]');
    if (toggle) {
        const filters = document.querySelector('[data-catalog-filters]');
        filters?.classList.toggle('hidden');
        toggle.setAttribute('aria-expanded', String(!filters?.classList.contains('hidden')));
    }
});

window.addEventListener('popstate', () => loadCatalog(window.location.href, false));
