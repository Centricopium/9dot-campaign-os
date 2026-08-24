<button
    type="button"
    data-campaign-mobile-back
    data-fallback-url="{{ url('/admin') }}"
    hidden
    aria-label="Go back to previous page"
>
    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none">
        <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
    <span>Back</span>
</button>

<style>
    [data-campaign-mobile-back] {
        display: none;
    }

    [data-campaign-mobile-back][hidden] {
        display: none !important;
    }

    @media (max-width: 768px) {
        [data-campaign-mobile-back] {
            position: fixed;
            z-index: 45;
            left: max(0.875rem, env(safe-area-inset-left));
            bottom: max(1rem, calc(env(safe-area-inset-bottom) + 0.75rem));
            display: inline-flex;
            min-height: 2.75rem;
            align-items: center;
            gap: 0.35rem;
            border: 1px solid rgba(196, 181, 253, 0.32);
            border-radius: 9999px;
            padding: 0.68rem 1rem 0.68rem 0.8rem;
            color: #fff;
            background: linear-gradient(135deg, #9333ea 0%, #6d28d9 58%, #18111f 100%);
            box-shadow: 0 14px 34px -12px rgba(76, 29, 149, 0.78);
            font-size: 0.82rem;
            font-weight: 750;
            line-height: 1;
            -webkit-tap-highlight-color: transparent;
            backdrop-filter: blur(14px);
        }

        [data-campaign-mobile-back] svg {
            width: 1.15rem;
            height: 1.15rem;
            flex: 0 0 auto;
        }

        [data-campaign-mobile-back]:active {
            transform: translateY(1px) scale(0.98);
        }
    }
</style>

<script>
    (() => {
        if (window.__campaignMobileNavigation?.initialized) {
            window.__campaignMobileNavigation.refresh();
            return;
        }

        const storageKey = '9dot-campaign-navigation-stack';
        const dashboardPath = '/admin';

        const state = window.__campaignMobileNavigation = {
            initialized: true,
            refresh: () => {},
        };

        const currentPath = () => `${window.location.pathname}${window.location.search}${window.location.hash}`;

        const readStack = () => {
            try {
                const stack = JSON.parse(window.sessionStorage.getItem(storageKey) || '[]');

                return Array.isArray(stack)
                    ? stack.filter((path) => typeof path === 'string' && path.startsWith('/admin'))
                    : [];
            } catch {
                return [];
            }
        };

        const writeStack = (stack) => {
            window.sessionStorage.setItem(storageKey, JSON.stringify(stack.slice(-30)));
        };

        const rememberCurrentPage = () => {
            const current = currentPath();

            if (! current.startsWith('/admin') || current.startsWith('/admin/login')) {
                return;
            }

            const stack = readStack();

            if (stack.at(-1) === current) {
                return;
            }

            if (stack.at(-2) === current) {
                stack.pop();
            } else {
                stack.push(current);
            }

            writeStack(stack);
        };

        const goBack = () => {
            const button = document.querySelector('[data-campaign-mobile-back]');
            const fallbackUrl = button?.dataset.fallbackUrl || dashboardPath;
            const current = currentPath();
            const stack = readStack();

            while (stack.length && stack.at(-1) === current) {
                stack.pop();
            }

            const previous = stack.pop();
            writeStack(previous ? [...stack, previous] : stack);

            window.location.assign(previous || fallbackUrl);
        };

        state.refresh = () => {
            rememberCurrentPage();

            const current = currentPath();
            const isDashboard = current === dashboardPath || current === `${dashboardPath}/`;
            const shouldShow = current.startsWith('/admin')
                && ! current.startsWith('/admin/login')
                && ! isDashboard;

            document.querySelectorAll('[data-campaign-mobile-back]').forEach((button) => {
                button.hidden = ! shouldShow;

                if (button.dataset.campaignBackBound !== 'true') {
                    button.dataset.campaignBackBound = 'true';
                    button.addEventListener('click', goBack);
                }
            });
        };

        document.addEventListener('livewire:navigated', state.refresh);
        window.addEventListener('popstate', state.refresh);
        state.refresh();
    })();
</script>
