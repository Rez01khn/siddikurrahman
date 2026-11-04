(() => {
    const header = document.querySelector('#site-header');
    const toggle = document.querySelector('#menu-toggle');
    const menu = document.querySelector('#mobile-menu');
    if (header && toggle && menu) {
        const updateHeader = () => header.classList.toggle('scrolled', header.dataset.solidHeader === 'true' || window.scrollY > 30 || !menu.hidden);
        const closeMenu = () => { menu.hidden = true; toggle.setAttribute('aria-expanded', 'false'); updateHeader(); };
        toggle.addEventListener('click', () => {
            menu.hidden = !menu.hidden;
            toggle.setAttribute('aria-expanded', String(!menu.hidden));
            updateHeader();
        });
        menu.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !menu.hidden) { closeMenu(); toggle.focus(); }
        });
        window.addEventListener('scroll', updateHeader, { passive: true });
        window.matchMedia('(min-width: 1280px)').addEventListener('change', event => { if (event.matches) closeMenu(); });
        updateHeader();
    }
    // Animation only changes position: content never waits invisibly for a callback.
    if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const observer = new IntersectionObserver(entries => entries.forEach(entry => {
            if (entry.isIntersecting) { entry.target.classList.add('entered'); observer.unobserve(entry.target); }
        }), { threshold: 0, rootMargin: '0px 0px 40px 0px' });
        document.querySelectorAll('.reveal').forEach(element => observer.observe(element));
    }
    const dialog = document.querySelector('#media-dialog');
    const image = dialog?.querySelector('img');
    const title = document.querySelector('#media-title');
    const status = document.querySelector('#media-status');
    if (dialog && image && title && status) {
        let previousTrigger;
        const showStatus = message => { status.textContent = message; status.hidden = false; };
        image.addEventListener('load', () => { status.hidden = true; });
        image.addEventListener('error', () => {
            image.hidden = true;
            showStatus('This photograph could not be loaded. Please close and try again.');
        });
        document.querySelectorAll('[data-gallery]').forEach(button => button.addEventListener('click', () => {
            if (typeof dialog.showModal !== 'function') { window.location.assign(button.dataset.gallery); return; }
            previousTrigger = button;
            image.hidden = false;
            title.textContent = button.dataset.caption;
            image.alt = button.dataset.caption;
            showStatus('Loading photograph…');
            image.src = button.dataset.gallery;
            dialog.showModal();
            document.body.classList.add('lightbox-open');
            if (image.complete && image.naturalWidth > 0) status.hidden = true;
        }));
        dialog.querySelector('[data-close-dialog]')?.addEventListener('click', () => dialog.close());
        dialog.addEventListener('click', event => {
            if (event.target !== dialog) return;
            const bounds = dialog.getBoundingClientRect();
            if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) dialog.close();
        });
        dialog.addEventListener('close', () => {
            document.body.classList.remove('lightbox-open');
            previousTrigger?.focus();
        });
    }
})();
