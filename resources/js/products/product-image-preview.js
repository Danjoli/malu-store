document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('productImages');
    const preview = document.getElementById('imagePreview');
    const stage = document.getElementById('productImageStage');
    const stageImage = document.getElementById('productImageStageImage');
    const stageEmpty = document.getElementById('productImageStageEmpty');

    if (!input || !preview || !stage || !stageImage || !stageEmpty) {
        return;
    }

    const initialThumbnails = preview.innerHTML;
    let objectUrls = [];

    const showImage = (src, alt) => {
        stageImage.src = src;
        stageImage.alt = alt;
        stageImage.classList.remove('hidden');
        stageEmpty.classList.add('hidden');
    };

    const showEmptyState = () => {
        stageImage.removeAttribute('src');
        stageImage.alt = '';
        stageImage.classList.add('hidden');
        stageEmpty.classList.remove('hidden');
    };

    const selectThumbnail = (button) => {
        preview.querySelectorAll('[data-preview-src]').forEach((thumbnail) => {
            thumbnail.classList.remove('border-[#cf7184]', 'ring-2', 'ring-[#f7dce2]');
            thumbnail.classList.add('border-transparent');
            thumbnail.setAttribute('aria-pressed', 'false');
        });

        button.classList.remove('border-transparent');
        button.classList.add('border-[#cf7184]', 'ring-2', 'ring-[#f7dce2]');
        button.setAttribute('aria-pressed', 'true');
        showImage(button.dataset.previewSrc, button.dataset.previewAlt || 'Prévia do produto');
    };

    const bindThumbnails = () => {
        const thumbnails = Array.from(preview.querySelectorAll('[data-preview-src]'));

        thumbnails.forEach((button) => {
            button.addEventListener('click', () => selectThumbnail(button));
        });

        if (thumbnails.length > 0) {
            selectThumbnail(thumbnails[0]);
        }
    };

    const restoreInitialGallery = () => {
        preview.innerHTML = initialThumbnails;

        if (initialThumbnails.trim()) {
            bindThumbnails();
            return;
        }

        const initialSrc = stage.dataset.initialSrc;

        if (initialSrc) {
            showImage(initialSrc, stage.dataset.initialAlt || 'Imagem do produto');
        } else {
            showEmptyState();
        }
    };

    input.addEventListener('change', () => {
        objectUrls.forEach((url) => URL.revokeObjectURL(url));
        objectUrls = [];
        preview.innerHTML = '';

        const files = Array.from(input.files).filter((file) => file.type.startsWith('image/'));

        if (files.length === 0) {
            restoreInitialGallery();
            return;
        }

        files.forEach((file) => {
            const url = URL.createObjectURL(file);
            objectUrls.push(url);

            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'product-image-thumbnail overflow-hidden rounded-lg border-2 border-transparent bg-[#f3ebe7] transition hover:border-[#cf7184]';
            button.dataset.previewSrc = url;
            button.dataset.previewAlt = `Prévia de ${file.name}`;
            button.setAttribute('aria-label', `Ampliar ${file.name}`);

            const image = document.createElement('img');
            image.src = url;
            image.alt = '';
            image.className = 'aspect-square w-full object-cover';

            button.appendChild(image);
            preview.appendChild(button);
        });

        bindThumbnails();
    });

    bindThumbnails();
});
