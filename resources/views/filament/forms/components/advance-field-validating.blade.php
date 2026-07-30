<div>
    <div
        id="advance-field-loading-overlay"
        wire:ignore
        style="display:none;position:fixed;inset:0;z-index:9999999;align-items:center;justify-content:center;background:rgba(0,0,0,0.45);backdrop-filter:blur(2px);"
    >
        <div style="background:#fff;border-radius:1rem;box-shadow:0 20px 60px rgba(0,0,0,0.25);display:flex;flex-direction:column;align-items:center;gap:0.75rem;padding:2rem 2.5rem;">
            <svg style="animation:advance-field-spin 1s linear infinite;width:2.5rem;height:2.5rem;color:#4f46e5;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle style="opacity:0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path style="opacity:0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span style="font-size:0.875rem;font-weight:600;color:#4b5563;">កំពុងផ្ទៀងផ្ទាត់...</span>
        </div>
    </div>

    <style>
        @keyframes advance-field-spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
    </style>

    <script>
        (function () {
            if (window.__advanceFieldOverlayRegistered) return;
            window.__advanceFieldOverlayRegistered = true;

            function handleAdvanceFieldBlur(el) {
                var cur = el.value;
                var prev = el.dataset.prevVal !== undefined ? el.dataset.prevVal : null;
                if (cur !== prev) {
                    el.dataset.prevVal = cur;
                    if (cur.trim() !== '') {
                        var overlay = document.getElementById('advance-field-loading-overlay');
                        if (overlay) overlay.style.display = 'flex';
                    }
                }
            }
            window.handleAdvanceFieldBlur = handleAdvanceFieldBlur;

            function hideAdvanceFieldOverlay() {
                var overlay = document.getElementById('advance-field-loading-overlay');
                if (overlay) overlay.style.display = 'none';
            }

            window.addEventListener('advance-field-validated', hideAdvanceFieldOverlay);

            function registerLivewireListener() {
                if (typeof Livewire !== 'undefined') {
                    Livewire.on('advance-field-validated', hideAdvanceFieldOverlay);
                }
            }
            registerLivewireListener();
            document.addEventListener('livewire:initialized', registerLivewireListener);
        })();
    </script>
</div>
