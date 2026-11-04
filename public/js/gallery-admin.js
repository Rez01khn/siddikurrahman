(() => {
    document.querySelectorAll('[data-gallery-batch]').forEach(input => {
        const grid = input.closest('form').querySelector('[data-gallery-previews]');
        let urls = [];
        const release = () => { urls.forEach(url => URL.revokeObjectURL(url)); urls = []; };
        input.addEventListener('change', () => {
            release(); grid.replaceChildren();
            if (input.files.length > 10) {
                const warning = document.createElement('p');
                warning.textContent = 'Choose up to ten images per upload.';
                grid.append(warning); return;
            }
            [...input.files].forEach(file => {
                const figure = document.createElement('figure');
                const caption = document.createElement('figcaption');
                caption.className = 'text-xs text-slate-500 mt-2 break-words';
                caption.textContent = file.name;
                if (['image/jpeg', 'image/png', 'image/webp'].includes(file.type) && file.size <= 5 * 1024 * 1024) {
                    const img = document.createElement('img');
                    const url = URL.createObjectURL(file); urls.push(url);
                    img.src = url; img.alt = 'Selected upload preview';
                    img.className = 'w-full h-40 object-contain border border-forest/15 rounded-lg';
                    img.addEventListener('error', () => { img.hidden = true; caption.textContent = file.name + ' — image could not be previewed.'; });
                    figure.append(img);
                } else { caption.textContent += ' — choose a JPEG, PNG or WebP up to 5 MB.'; }
                figure.append(caption); grid.append(figure);
            });
        });
        window.addEventListener('pagehide', release);
    });
})();
