document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('productImages');
    const preview = document.getElementById('imagePreview');

    if (!input || !preview) {
        return;
    }

    input.addEventListener('change', () => {

        preview.innerHTML = '';

        const files = Array.from(input.files);

        files.forEach(file => {

            if (!file.type.startsWith('image/')) {
                return;
            }

            const reader = new FileReader();

            reader.onload = (event) => {

                const container = document.createElement('div');

                container.classList.add(
                    'border',
                    'rounded-lg',
                    'p-2',
                    'bg-gray-50'
                );

                const image = document.createElement('img');

                image.src = event.target.result;
                image.alt = file.name;

                image.classList.add(
                    'w-full',
                    'h-40',
                    'object-cover',
                    'rounded'
                );

                const name = document.createElement('p');

                name.textContent = file.name;

                name.classList.add(
                    'text-sm',
                    'text-gray-600',
                    'mt-2',
                    'truncate'
                );

                container.appendChild(image);
                container.appendChild(name);

                preview.appendChild(container);
            };

            reader.readAsDataURL(file);
        });
    });

});
