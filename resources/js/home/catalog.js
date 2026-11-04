const homeShellSelector = '[data-home-catalog-shell]';
let homeRequestController;
let homeSearchTimer;

const setHomeLoading = (loading) => {
    const shell = document.querySelector(homeShellSelector);
    if (!shell) return;

    shell.setAttribute('aria-busy', String(loading));
    shell.classList.toggle('opacity-60', loading);
    shell.classList.toggle('pointer-events-none', loading);
};

const loadHomeCatalog = async (url, pushState = true, keepSearchFocus = false) => {
    homeRequestController?.abort();
    const controller = new AbortController();
    homeRequestController = controller;
    setHomeLoading(true);

    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: controller.signal,
        });

        if (!response.ok) throw new Error('Não foi possível atualizar a vitrine.');

        const result = new DOMParser().parseFromString(await response.text(), 'text/html');
        const nextShell = result.querySelector(homeShellSelector);
        const currentShell = document.querySelector(homeShellSelector);
        if (!nextShell || !currentShell) throw new Error('Resposta da vitrine inválida.');

        currentShell.replaceWith(nextShell);
        if (pushState) window.history.pushState({}, '', url);

        if (keepSearchFocus) {
            const search = document.querySelector(`${homeShellSelector} [name="search"]`);
            search?.focus({ preventScroll: true });
            search?.setSelectionRange(search.value.length, search.value.length);
        }
    } catch (error) {
        if (error.name !== 'AbortError') window.location.assign(url);
    } finally {
        if (homeRequestController === controller) setHomeLoading(false);
    }
};

const submitHomeFilters = (form, keepSearchFocus = false) => {
    const params = new URLSearchParams();

    new FormData(form).forEach((value, key) => {
        if (String(value).trim() !== '' && !(key === 'sort' && value === 'recent')) params.append(key, value);
    });

    loadHomeCatalog(`${form.action}${params.size ? `?${params}` : ''}#produtos`, true, keepSearchFocus);
};

document.addEventListener('submit', (event) => {
    const form = event.target.closest('[data-home-filter-form]');
    if (!form) return;
    event.preventDefault();
    submitHomeFilters(form);
});

document.addEventListener('input', (event) => {
    const form = event.target.closest('[data-home-filter-form]');
    if (!form || !event.target.matches('[data-home-live-search]')) return;

    homeRequestController?.abort();
    window.clearTimeout(homeSearchTimer);
    homeSearchTimer = window.setTimeout(() => submitHomeFilters(form, true), 180);
});

document.addEventListener('change', (event) => {
    const form = event.target.closest('[data-home-filter-form]');
    if (!form || !event.target.matches('[data-home-filter-control]')) return;
    submitHomeFilters(form);
});

document.addEventListener('click', (event) => {
    const link = event.target.closest('[data-home-filter-link]');
    if (!link || event.ctrlKey || event.metaKey || event.shiftKey) return;

    event.preventDefault();
    loadHomeCatalog(link.href);
});

window.addEventListener('popstate', () => loadHomeCatalog(window.location.href, false));
