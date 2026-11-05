import './bootstrap';
import './admin/live-filters';

import Alpine from '@alpinejs/csp';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.Swal = Swal;

document.addEventListener('submit', (event) => {
    const form = event.target.closest('form[data-confirm]');

    if (form && !window.confirm(form.dataset.confirm)) {
        event.preventDefault();
    }
});

document.addEventListener('click', (event) => {
    const removeButton = event.target.closest('[data-remove-parent]');

    if (removeButton) {
        removeButton.parentElement?.remove();
    }
});

Alpine.data('storeHeader', () => ({
    searchOpen: false,
    menuOpen: false,
    dark: document.documentElement.classList.contains('dark'),
    toggleTheme() {
        this.dark = !this.dark;
        window.storeTheme.set(this.dark);
    },
    toggleSearch() {
        this.searchOpen = !this.searchOpen;
        this.$nextTick(() => this.$refs.searchInput?.focus());
    },
}));

Alpine.data('adminLayout', () => ({
    adminMenuOpen: false,
    sidebarCollapsed: false,
}));

Alpine.data('dropdown', () => ({ open: false }));
Alpine.data('addressEditor', () => ({ open: false }));

Alpine.start();
