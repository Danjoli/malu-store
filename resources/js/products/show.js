import './shipping-calculator';

document.addEventListener('DOMContentLoaded', () => {
    const sizeGuideDialog = document.querySelector('[data-size-guide-dialog]');
    document.querySelector('[data-size-guide-open]')?.addEventListener('click', () => sizeGuideDialog?.showModal());
    sizeGuideDialog?.querySelector('[data-size-guide-close]')?.addEventListener('click', () => sizeGuideDialog.close());
    sizeGuideDialog?.addEventListener('click', (event) => {
        if (event.target === sizeGuideDialog) sizeGuideDialog.close();
    });

    const tabs = document.querySelector('[data-product-tabs]');

    if (tabs) {
        const activateTab = (name, focus = false) => {
            tabs.querySelectorAll('[data-product-tab]').forEach((button) => {
                const active = button.dataset.productTab === name;
                button.setAttribute('aria-selected', String(active));
                button.classList.toggle('border-[#d66f7c]', active);
                button.classList.toggle('border-transparent', !active);
                if (active && focus) button.focus();
            });

            tabs.querySelectorAll('[data-product-panel]').forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.productPanel !== name);
            });
        };

        tabs.querySelectorAll('[data-product-tab]').forEach((button) => {
            button.addEventListener('click', () => activateTab(button.dataset.productTab));
            button.addEventListener('keydown', (event) => {
                if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
                const buttons = [...tabs.querySelectorAll('[data-product-tab]')];
                const direction = event.key === 'ArrowRight' ? 1 : -1;
                const next = buttons[(buttons.indexOf(button) + direction + buttons.length) % buttons.length];
                activateTab(next.dataset.productTab, true);
            });
        });

        if (window.location.hash === '#avaliacoes' || tabs.querySelector('.text-red-600')) {
            activateTab('reviews');
            tabs.scrollIntoView({ block: 'start' });
        }
    }

    const gallery = document.querySelector('[data-product-gallery]');
    const mainImage = gallery?.querySelector('[data-product-gallery-main]');

    gallery?.querySelectorAll('[data-product-gallery-thumbnail]').forEach((thumbnail) => {
        thumbnail.addEventListener('click', () => {
            mainImage.src = thumbnail.dataset.productGalleryThumbnail;
            mainImage.alt = thumbnail.dataset.productGalleryAlt || mainImage.alt;
        });
    });

    const form = document.querySelector('[data-product-variant-form]');

    if (!form) {
        return;
    }

    const variants = JSON.parse(form.dataset.productVariants || '[]');
    const variantInput = form.querySelector('[name="variant_id"]');
    const selectedColorName = form.querySelector('[data-selected-color-name]');
    const stockMessage = form.querySelector('[data-variant-stock]');
    let color = form.dataset.productInitialColor;
    let size = form.dataset.productInitialSize;

    const sync = () => {
        const variant = variants.find((item) => item.color === color && item.size === size);

        if (variant) {
            variantInput.value = variant.id;
            if (stockMessage) {
                stockMessage.textContent = variant.stock <= 5
                    ? `Últimas ${variant.stock} unidades nesta combinação.`
                    : 'Em estoque e pronto para envio.';
                stockMessage.classList.toggle('font-bold', variant.stock <= 5);
                stockMessage.classList.toggle('text-[#b44259]', variant.stock <= 5);
            }
        } else {
            variantInput.value = '';
            if (stockMessage) stockMessage.textContent = 'Selecione outra combinação disponível.';
        }

        form.querySelectorAll('[data-product-size]').forEach((button) => {
            const available = variants.some((item) => item.color === color && item.size === button.dataset.productSize);
            button.disabled = !available;
            button.setAttribute('aria-pressed', String(button.dataset.productSize === size && available));
        });

        form.querySelectorAll('[data-product-color]').forEach((button) => {
            button.setAttribute('aria-pressed', String(button.dataset.productColor === color));
        });

        if (selectedColorName) {
            selectedColorName.textContent = color || 'Única';
        }
    };

    form.querySelectorAll('[data-product-color]').forEach((button) => {
        button.addEventListener('click', () => {
            color = button.dataset.productColor;
            const matchingSize = variants.find((item) => item.color === color && item.size === size);
            size = matchingSize ? size : variants.find((item) => item.color === color)?.size;
            sync();
        });
    });

    form.querySelectorAll('[data-product-size]').forEach((button) => {
        button.addEventListener('click', () => {
            size = button.dataset.productSize;
            sync();
        });
    });

    sync();
});
