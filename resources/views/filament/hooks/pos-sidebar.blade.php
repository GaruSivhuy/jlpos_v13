{{--
    The POS page collapses the sidebar and every other page brings it back. This runs on every page render (full load
    and SPA navigation) and keeps the sidebar state from before the POS in its own key, so it does not rely on the
    POS page being torn down cleanly (a refresh never gave Filament's persisted state the chance to be written back).
--}}
<div
    x-data="{
        isPos: @js($isPos),
        storageKey: 'jlpos.sidebarBeforePos',
        isDesktop() {
            return window.innerWidth >= 1024
        },
        init() {
            let saved = null

            try {
                saved = localStorage.getItem(this.storageKey)
            } catch (e) {}

            if (this.isPos) {
                if (! this.isDesktop()) {
                    return
                }

                if (saved === null) {
                    try {
                        localStorage.setItem(this.storageKey, JSON.stringify($store.sidebar.isOpen))
                    } catch (e) {}
                }

                $store.sidebar.close()

                return
            }

            if (saved === null) {
                return
            }

            const wasOpen = JSON.parse(saved) === true

            $store.sidebar.isOpenDesktop = wasOpen

            if (this.isDesktop()) {
                $store.sidebar.isOpen = wasOpen
            }

            try {
                localStorage.removeItem(this.storageKey)
            } catch (e) {}
        },
    }"
    class="hidden"
></div>
