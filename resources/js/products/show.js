import './shipping-calculator';

document.addEventListener('DOMContentLoaded', () => {
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
    let color = form.dataset.productInitialColor;
    let size = form.dataset.productInitialSize;

    const sync = () => {
        const variant = variants.find((item) => item.color === color && item.size === size);

        if (variant) {
            variantInput.value = variant.id;
        }

        form.querySelectorAll('[data-product-size]').forEach((button) => {
            const available = variants.some((item) => item.color === color && item.size === button.dataset.productSize);
            button.disabled = !available;
            button.classList.toggle('opacity-40', !available);
            button.classList.toggle('cursor-not-allowed', !available);
            button.classList.toggle('border-[#d66f7c]', button.dataset.productSize === size && available);
            button.classList.toggle('bg-[#fff1ef]', button.dataset.productSize === size && available);
        });

        form.querySelectorAll('[data-product-color]').forEach((button) => {
            button.setAttribute('aria-pressed', String(button.dataset.productColor === color));
            button.classList.toggle('ring-2', button.dataset.productColor === color);
            button.classList.toggle('ring-[#d66f7c]', button.dataset.productColor === color);
        });
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
