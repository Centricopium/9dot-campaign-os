<div
    data-pwa-ios-guide
    hidden
    class="fixed inset-0 z-[100] grid place-items-center bg-black/70 p-4 backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
    aria-labelledby="pwa-ios-guide-title"
>
    <div class="w-full max-w-sm rounded-2xl border border-purple-500/30 bg-zinc-950 p-6 text-white shadow-2xl">
        <div class="mb-4 flex items-start gap-3">
            <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-purple-600/20 text-purple-300">
                <x-filament::icon icon="heroicon-o-device-phone-mobile" class="h-6 w-6" />
            </div>
            <div>
                <h2 id="pwa-ios-guide-title" class="text-base font-bold">Install on iPhone or iPad</h2>
                <p class="mt-1 text-sm leading-6 text-zinc-300">
                    Open this page in Safari, tap the Share button, then choose <strong class="text-white">Add to Home Screen</strong>.
                </p>
            </div>
        </div>
        <button
            type="button"
            data-pwa-ios-close
            class="w-full rounded-xl bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-purple-500"
        >
            Got it
        </button>
    </div>
</div>

<script>
    (() => {
        if (window.__nineDotPwa?.initialized) {
            window.__nineDotPwa.refresh?.();
            return;
        }

        const state = window.__nineDotPwa = {
            initialized: true,
            deferredPrompt: null,
            refresh: null,
        };

        const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent)
            || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
        const isStandalone = () => window.matchMedia('(display-mode: standalone)').matches
            || window.navigator.standalone === true;

        const iosGuide = () => document.querySelector('[data-pwa-ios-guide]');

        const closeIosGuide = () => {
            const guide = iosGuide();

            if (guide) {
                guide.hidden = true;
            }
        };

        const handleInstall = async () => {
            if (isStandalone()) {
                return;
            }

            if (state.deferredPrompt) {
                state.deferredPrompt.prompt();
                await state.deferredPrompt.userChoice;
                state.deferredPrompt = null;
                state.refresh();
                return;
            }

            if (isIos) {
                const guide = iosGuide();

                if (guide) {
                    guide.hidden = false;
                }
            }
        };

        state.refresh = () => {
            const shouldShow = ! isStandalone() && (Boolean(state.deferredPrompt) || isIos);

            document.querySelectorAll('[data-pwa-install]').forEach((button) => {
                button.hidden = ! shouldShow;

                if (button.dataset.pwaBound !== 'true') {
                    button.dataset.pwaBound = 'true';
                    button.addEventListener('click', handleInstall);
                }
            });

            document.querySelectorAll('[data-pwa-ios-close]').forEach((button) => {
                if (button.dataset.pwaBound !== 'true') {
                    button.dataset.pwaBound = 'true';
                    button.addEventListener('click', closeIosGuide);
                }
            });
        };

        window.addEventListener('beforeinstallprompt', (event) => {
            event.preventDefault();
            state.deferredPrompt = event;
            state.refresh();
        });

        window.addEventListener('appinstalled', () => {
            state.deferredPrompt = null;
            state.refresh();
        });

        document.addEventListener('livewire:navigated', state.refresh);
        state.refresh();

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js', { scope: '/' });
            }, { once: true });
        }
    })();
</script>
