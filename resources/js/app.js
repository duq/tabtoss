import Alpine from 'alpinejs'
import intersect from '@alpinejs/intersect'

// plugins have to be imported before Alpine is started
Alpine.plugin(intersect)

const themeStorageKey = 'theme'

function readStoredTheme() {
    try {
        return window.localStorage.getItem(themeStorageKey)
    } catch (error) {
        return null
    }
}

function writeStoredTheme(theme) {
    try {
        window.localStorage.setItem(themeStorageKey, theme)
    } catch (error) {
        // Ignore storage failures in private browsing or locked-down environments.
    }
}

function resolveTheme(theme) {
    if (theme === 'dark' || theme === 'light') {
        return theme
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
}

function applyTheme(theme, persist = false) {
    const resolvedTheme = resolveTheme(theme)
    const root = document.documentElement

    root.classList.toggle('dark', resolvedTheme === 'dark')
    root.setAttribute('data-theme', resolvedTheme)
    root.style.colorScheme = resolvedTheme

    if (persist) {
        writeStoredTheme(resolvedTheme)
    }

    window.dispatchEvent(new CustomEvent('theme-changed', {
        detail: {
            theme: resolvedTheme,
        },
    }))

    return resolvedTheme
}

window.applyThemePreference = (theme) => applyTheme(theme, true)
window.toggleTheme = () => applyTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark', true)
window.themeToggle = () => ({
    isDark: document.documentElement.classList.contains('dark'),
    init() {
        this.syncTheme()
        window.addEventListener('theme-changed', () => {
            this.syncTheme()
        })
    },
    syncTheme() {
        this.isDark = document.documentElement.classList.contains('dark')
    },
    toggleTheme() {
        window.toggleTheme()
    },
})

const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)')

if (prefersDarkScheme.addEventListener) {
    prefersDarkScheme.addEventListener('change', () => {
        if (! readStoredTheme()) {
            applyTheme(null)
        }
    })
}

applyTheme(readStoredTheme())

document.addEventListener('DOMContentLoaded', function () {
    assignTabSliderEvents();
});

function assignTabSliderEvents() {
    // do that for each .tab-slider
    let tabSliders = document.querySelectorAll(".tab-slider")

    tabSliders.forEach(tabSlider => {
        let tabs = tabSlider.querySelectorAll(".tab")
        let panels = tabSlider.querySelectorAll(".tab-panel")

        tabs.forEach(tab => {
            tab.addEventListener("click", ()=>{
                let tabTarget = tab.getAttribute("aria-controls")
                // set all tabs as not active
                tabs.forEach(tab =>{
                    tab.setAttribute("data-active-tab", "false")
                    tab.setAttribute("aria-selected", "false")
                })

                // set the clicked tab as active
                tab.setAttribute("data-active-tab", "true")
                tab.setAttribute("aria-selected", "true")

                panels.forEach(panel =>{
                    let panelId = panel.getAttribute("id")
                    if(tabTarget === panelId){
                        panel.classList.remove("hidden", "opacity-0")
                        panel.classList.add("block", "opacity-100")
                        // animate panel fade in

                        panel.animate([
                            { opacity: 0, maxHeight: 0 },
                            { opacity: 1, maxHeight: "100%" }
                        ], {
                            duration: 500,
                            easing: "ease-in-out",
                            fill: "forwards"
                        })

                    } else {
                        panel.classList.remove("block", "opacity-100")
                        panel.classList.add("hidden", "opacity-0")

                        // animate panel fade out
                        panel.animate([
                            { opacity: 1, maxHeight: "100%" },
                            { opacity: 0, maxHeight: 0 }
                        ], {
                            duration: 500,
                            easing: "ease-in-out",
                            fill: "forwards"
                        })
                    }
                })
            })
        })

        let activeTab = tabSlider.querySelector(".tab[data-active-tab='true']")
        activeTab.click()
    })

}

window.Alpine = Alpine
