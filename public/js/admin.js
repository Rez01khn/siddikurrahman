(() => {
    const sidebar = document.querySelector('#admin-sidebar');
    const toggle = document.querySelector('#sidebar-toggle');
    const backdrop = document.querySelector('#sidebar-backdrop');
    const close = document.querySelector('#sidebar-close');
    if (sidebar && toggle && backdrop && close) {
        const setOpen = (open, restoreFocus = false) => {
            sidebar.classList.toggle('-translate-x-full', !open);
            backdrop.hidden = !open;
            toggle.setAttribute('aria-expanded', String(open));
            document.body.classList.toggle('sidebar-open', open);
            sidebar.inert = !open && !window.matchMedia('(min-width: 1024px)').matches;
            if (open) close.focus();
            else if (restoreFocus) toggle.focus();
        };
        toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
        close.addEventListener('click', () => setOpen(false, true));
        backdrop.addEventListener('click', () => setOpen(false, true));
        document.addEventListener('keydown', event => {
            if (backdrop.hidden) return;
            if (event.key === 'Escape') setOpen(false, true);
            if (event.key === 'Tab') {
                const focusable = [...sidebar.querySelectorAll('a,button')].filter(element => element.getClientRects().length);
                const first = focusable[0];
                const last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
                else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
            }
        });
        window.matchMedia('(min-width: 1024px)').addEventListener('change', () => setOpen(false));
        setOpen(false);
    }
    const profile = document.querySelector('#profile-menu');
    if (profile) {
        document.addEventListener('click', event => { if (!profile.contains(event.target)) profile.open = false; });
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && profile.open) { profile.open = false; profile.querySelector('summary').focus(); }
        });
    }
    document.querySelectorAll('[data-image-upload]').forEach(input => {
        const preview = document.getElementById(input.id + '-preview');
        const status = document.getElementById(input.id + '-preview-status');
        let objectUrl;
        input.addEventListener('change', () => {
            if (objectUrl) URL.revokeObjectURL(objectUrl);
            preview.hidden = true;
            status.textContent = '';
            const file = input.files[0];
            if (!file) return;
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
                status.textContent = 'Choose a JPEG, PNG, or WebP image up to 5 MB. Server validation will check the file too.';
                return;
            }
            objectUrl = URL.createObjectURL(file);
            preview.src = objectUrl;
            preview.hidden = false;
            status.textContent = 'Preview only. Save the section to upload this image.';
        });
        preview.addEventListener('error', () => {
            preview.hidden = true;
            status.textContent = 'This image could not be previewed. Please choose a different file.';
        });
        window.addEventListener('pagehide', () => { if (objectUrl) URL.revokeObjectURL(objectUrl); });
    });
})();
