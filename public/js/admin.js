(() => {
    const sidebar = document.querySelector('#admin-sidebar');
    const toggle = document.querySelector('#sidebar-toggle');
    const backdrop = document.querySelector('#sidebar-backdrop');
    const close = document.querySelector('#sidebar-close');
    const offBtn = document.querySelector('#sidebar-off-btn');
    const statusText = document.querySelector('#topbar-sidebar-status');

    if (sidebar) {
        const isDesktop = () => window.matchMedia('(min-width: 1024px)').matches;

        // Restore saved preference on desktop
        if (isDesktop() && localStorage.getItem('sr_admin_sidebar_collapsed') === '1') {
            document.body.classList.add('sidebar-collapsed');
        }

        const updateStatusText = () => {
            if (!statusText) return;
            const isOff = isDesktop()
                ? document.body.classList.contains('sidebar-collapsed')
                : sidebar.classList.contains('-translate-x-full');
            statusText.textContent = isOff ? 'OFF' : 'ON';
            if (toggle) {
                toggle.setAttribute('aria-expanded', String(!isOff));
            }
        };

        const turnOff = () => {
            if (isDesktop()) {
                document.body.classList.add('sidebar-collapsed');
                localStorage.setItem('sr_admin_sidebar_collapsed', '1');
            } else {
                sidebar.classList.add('-translate-x-full');
                if (backdrop) backdrop.hidden = true;
                document.body.classList.remove('sidebar-open');
            }
            updateStatusText();
        };

        const turnOn = () => {
            if (isDesktop()) {
                document.body.classList.remove('sidebar-collapsed');
                localStorage.setItem('sr_admin_sidebar_collapsed', '0');
            } else {
                sidebar.classList.remove('-translate-x-full');
                if (backdrop) backdrop.hidden = false;
                document.body.classList.add('sidebar-open');
            }
            updateStatusText();
        };

        const toggleSidebar = () => {
            const isCurrentlyOff = isDesktop()
                ? document.body.classList.contains('sidebar-collapsed')
                : sidebar.classList.contains('-translate-x-full');
            if (isCurrentlyOff) {
                turnOn();
            } else {
                turnOff();
            }
        };

        if (offBtn) {
            offBtn.addEventListener('click', (e) => {
                e.preventDefault();
                turnOff();
            });
        }

        if (toggle) {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                toggleSidebar();
            });
        }

        if (close) {
            close.addEventListener('click', turnOff);
        }

        if (backdrop) {
            backdrop.addEventListener('click', turnOff);
        }

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                if (backdrop && !backdrop.hidden) turnOff();
            }
        });

        window.matchMedia('(min-width: 1024px)').addEventListener('change', () => {
            if (isDesktop()) {
                if (backdrop) backdrop.hidden = true;
                document.body.classList.remove('sidebar-open');
                if (localStorage.getItem('sr_admin_sidebar_collapsed') === '1') {
                    document.body.classList.add('sidebar-collapsed');
                } else {
                    document.body.classList.remove('sidebar-collapsed');
                }
            } else {
                sidebar.classList.add('-translate-x-full');
            }
            updateStatusText();
        });

        updateStatusText();
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
