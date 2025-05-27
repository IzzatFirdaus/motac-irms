/**
 * Template Customizer
 * Allows to change options dynamically and persists them in localStorage.
 * Interacts with server-side settings via HTML attributes and expects specific
 * CSS naming conventions for LTR/RTL and light/dark modes.
 *
 */

// Assuming SCSS and HTML for the customizer panel are compiled/managed separately
// and the panel's basic HTML structure exists or is injected by _setupCustomizerUIPanel.

const CSS_FILENAME_PATTERN = '%name%.css'; // e.g., theme-default.css, core-dark.css
const DEFAULT_CONTROLS = [
    'rtl',
    'style',
    'layoutType',
    // 'layoutMenuFlipped', // Uncomment if used
    'showDropdownOnHover', // Primarily for horizontal menu
    'layoutNavbarFixed',
    'layoutFooterFixed',
    'themes'
];
const DEFAULT_STYLES = ['light', 'dark'];

// Initial values derived from the server-rendered HTML (commonMaster.blade.php)
const initialHtmlElement = document.documentElement;
const initialDataTheme = initialHtmlElement.getAttribute('data-theme') || 'theme-default';
const initialDataStyle = initialHtmlElement.classList.contains('dark-style') ? 'dark' : 'light';
const initialDataTextDirIsRtl = initialHtmlElement.getAttribute('dir') === 'rtl';
// Add other initial HTML-derived settings as needed, e.g., for layout type, menu collapsed state

class TemplateCustomizer {
    constructor({
        cssPath = 'assets/vendor/css', // Default path for core CSS
        themesPath = 'assets/vendor/css', // Default path for theme CSS (often same as core)
        cssFilenamePattern = CSS_FILENAME_PATTERN,
        displayCustomizer = true, // Whether to show the customizer UI panel
        controls = DEFAULT_CONTROLS,
        // Server-side defaults passed during instantiation (from commonMaster -> scriptsIncludes)
        defaultTextDir = initialDataTextDirIsRtl ? 'rtl' : 'ltr',
        defaultStyle = initialDataStyle,
        availableThemes = TemplateCustomizer.THEMES, // Static property
        defaultThemeName = initialDataTheme,
        // Function to resolve asset paths, incorporating $configData['rtlSupport']
        pathResolver = (path) => path, //
        onSettingsChange = () => {},
        lang = initialHtmlElement.getAttribute('lang')?.split('-')[0] || 'en',
        // This flag indicates if the theme has separate RTL CSS files (e.g., core-rtl.css)
        // Should be passed from PHP based on config('custom.custom.myRTLSupport')
        rtlAssetsEnabled = true // Default to true, but should be configured
    }) {
        if (this._isSSR) return;
        if (!window.Helpers) {
            console.error('TemplateCustomizer Error: window.Helpers is required.');
            return;
        }

        this.settings = {
            cssPath,
            themesPath,
            cssFilenamePattern,
            displayCustomizer,
            controls,
            availableThemes,
            styles: DEFAULT_STYLES, // Hardcoded for now, can be made configurable
            lang,
            pathResolver,
            onSettingsChange,
            rtlAssetsEnabled
        };

        // Initialize settings based on priority: localStorage > constructor defaults
        this.settings.rtl = (this._getSetting('Rtl') === 'true') ?? (defaultTextDir === 'rtl');
        this.settings.style = DEFAULT_STYLES.includes(this._getSetting('Style')) ? this._getSetting('Style') : defaultStyle;
        if (!DEFAULT_STYLES.includes(this.settings.style)) this.settings.style = DEFAULT_STYLES[0];

        const storedThemeName = this._getSetting('Theme');
        this.settings.theme = this._getThemeByName(storedThemeName) || this._getThemeByName(defaultThemeName) || this.settings.availableThemes[0];

        // Initialize other settings from localStorage or defaults (e.g., layoutType, fixedNavbar)
        // this.settings.layoutType = this._getSetting('LayoutType') || defaultLayoutType; // Example

        this._listeners = [];
        this._controls = {}; // To store references to UI control elements
        this._isloadingTheme = false; // Flag for theme loading state

        // Apply initial settings to the document
        this._applyInitialSettings();

        // Setup the customizer UI panel
        if (this.settings.displayCustomizer) {
            this._setupCustomizerUIPanel();
        }
        console.log('TemplateCustomizer initialized with settings:', this.settings);
    }

    _applyInitialSettings() {
        this._initDirection(); // Sets dir attribute on <html>
        // Note: CSS files (core and theme) are now linked statically by styles.blade.php
        // based on $configData. This JS customizer will dynamically CHANGE them if user interacts.
        // Initial load uses server-rendered links.
    }

    _initDirection() {
        initialHtmlElement.setAttribute('dir', this.settings.rtl ? 'rtl' : 'ltr');
    }

    // Dynamically update a stylesheet link in the <head>
    _updateStylesheet(id, href) {
        let linkElement = document.getElementById(id);
        if (!linkElement) {
            linkElement = document.createElement('link');
            linkElement.id = id;
            linkElement.setAttribute('rel', 'stylesheet');
            linkElement.setAttribute('type', 'text/css');
            // Insert before the first script or style in head for better order
            const firstScriptOrStyle = document.head.querySelector('script, link[rel="stylesheet"], style');
            document.head.insertBefore(linkElement, firstScriptOrStyle);
        }
        if (linkElement.getAttribute('href') !== href) {
            linkElement.setAttribute('href', href);
            console.log(`TemplateCustomizer: Stylesheet '${id}' updated to: ${href}`);
        }
    }

    _getCoreCssPath() {
        const rtlSuffix = (this.settings.rtl && this.settings.rtlAssetsEnabled) ? (this.settings.pathResolver('').includes('/rtl') ? '' : '/rtl') : ''; // Logic from Helpers.php for rtlSupport
        const styleSuffix = this.settings.style !== 'light' ? `-${this.settings.style}` : '';
        const coreCssFile = `core${styleSuffix}.css`;
        // pathResolver should handle the /rtl/ part if configured for it.
        // If pathResolver ALREADY adds /rtl/, then rtlSuffix here might be redundant or need adjustment.
        // Assuming pathResolver in scriptsIncludes.blade.php is: path => (configData.assetsPath + 'vendor/css' + configData.rtlSupport + path)
        // Then, we just need to pass the filename part.
        return this.settings.pathResolver(this.settings.cssFilenamePattern.replace('%name%', `core${styleSuffix}`));
    }

    _getThemeCssPath(themeName) {
        const styleSuffix = this.settings.style !== 'light' ? `-${this.settings.style}` : '';
        // Similar to _getCoreCssPath, pathResolver is key
        return this.settings.pathResolver(this.settings.cssFilenamePattern.replace('%name%', `${themeName}${styleSuffix}`));
    }

    setRtl(isRtlEnabled) { // isRtlEnabled is boolean
        if (!this.settings.controls.includes('rtl') || !this.settings.rtlAssetsEnabled) return;

        this._setSetting('Rtl', String(isRtlEnabled));
        // A page reload is the most reliable way to apply RTL/LTR changes thoroughly.
        // The server will then render with the correct dir and CSS paths.
        window.location.reload();
    }

    setStyle(newStyle) { // newStyle is 'light' or 'dark'
        if (!this.settings.controls.includes('style') || !DEFAULT_STYLES.includes(newStyle)) return;

        this._setSetting('Style', newStyle);
        // Reload to allow server to generate correct CSS links and classes.
        window.location.reload();
    }

    setTheme(themeName, updateStorage = true, callback = null) {
        if (!this.settings.controls.includes('themes') || this._isloadingTheme) return;

        const themeObject = this._getThemeByName(themeName);
        if (!themeObject) {
            console.warn(`TemplateCustomizer: Theme '${themeName}' not found.`);
            return;
        }

        this._isloadingTheme = true;
        this._loadingState(true, true); // Show loading state on customizer UI

        this.settings.theme = themeObject;
        if (updateStorage) this._setSetting('Theme', themeName);

        // Dynamically update the theme CSS file
        // The pathResolver passed to constructor should handle RTL path segment
        const newThemePath = this._getThemeCssPath(themeName);
        const themeLinkElement = document.querySelector('.template-customizer-theme-css'); // From styles.blade.php

        if (themeLinkElement) {
            const tempLink = document.createElement('link');
            tempLink.setAttribute('rel', 'stylesheet');
            tempLink.setAttribute('type', 'text/css');
            tempLink.setAttribute('href', newThemePath);

            tempLink.onload = () => {
                themeLinkElement.setAttribute('href', newThemePath);
                this._isloadingTheme = false;
                this._loadingState(false, true);
                if (updateStorage) this.settings.onSettingsChange.call(this, this.settings);
                if (callback) callback();
                console.log(`TemplateCustomizer: Theme changed to '${themeName}'.`);
            };
            tempLink.onerror = () => {
                console.error(`TemplateCustomizer: Failed to load theme CSS '${newThemePath}'.`);
                this._isloadingTheme = false;
                this._loadingState(false, true);
                // Optionally revert to old theme or show error
                if (callback) callback(false); // Indicate failure
            };
            // Append tempLink to head to start loading, it won't be visible.
            // Once loaded, the actual theme link's href is updated.
            // This is a common pattern to preload CSS.
        } else {
            console.error("TemplateCustomizer: '.template-customizer-theme-css' link tag not found.");
            this._isloadingTheme = false;
            this._loadingState(false, true);
        }
    }

    // Example other setters (adapt as needed from original file)
    setLayoutType(type, updateStorage = true) {
        if (!this.settings.controls.includes('layoutType')) return;
        // Add/remove classes on body/html via window.Helpers
        // window.Helpers.setLayoutType(type); // Assuming Helpers method exists
        this.settings.layoutType = type;
        if (updateStorage) this._setSetting('LayoutType', type);
        if (updateStorage) this.settings.onSettingsChange.call(this, this.settings);
    }
    // ... Implement other setters like setLayoutNavbarFixed, setLayoutFooterFixed similarly

    setLang(lang, force = false) {
        if (lang === this.settings.lang && !force) return;
        if (!TemplateCustomizer.LANGUAGES[lang]) {
            console.warn(`TemplateCustomizer: Language "${lang}" not found in L10N. Falling back to 'en'.`);
            lang = 'en';
        }
        this.settings.lang = lang;
        // No need to save customizer lang to localStorage unless desired for customizer persistence independent of app locale
        // this._setSetting('Lang', lang);

        const translations = TemplateCustomizer.LANGUAGES[this.settings.lang];
        if (!this.container || !translations) return;

        Object.keys(translations).forEach(key => {
            const el = this.container.querySelector(`.template-customizer-t-${key}`);
            if (el) {
                // Handle nested 'themes' translations
                if (key === 'themes' && typeof translations[key] === 'object') {
                    const themeOptions = this.container.querySelectorAll('.template-customizer-theme-item');
                    themeOptions.forEach(themeOption => {
                        const radio = themeOption.querySelector('input[type="radio"]');
                        const label = themeOption.querySelector('.form-check-label');
                        if (radio && label) {
                            const themeName = radio.value;
                            const themeObj = this._getThemeByName(themeName);
                            label.textContent = translations[key][themeName] || themeObj?.title || themeName;
                        }
                    });
                } else {
                    el.textContent = translations[key];
                }
            }
        });
    }

    _setupCustomizerUIPanel() {
        if (!this.settings.displayCustomizer || document.getElementById('template-customizer-wrapper')) return;

        const wrapper = document.createElement('div');
        wrapper.id = 'template-customizer-wrapper';
        // Simplified: Assume customizerMarkup is a string containing the HTML structure
        // In a real scenario, you'd fetch or define this HTML.
        // This HTML needs to match the querySelectors used for controls.
        wrapper.innerHTML = `
            <div class="template-customizer Cs" id="template-customizer">
                <button class="template-customizer-open-btn btn btn-primary p-2 rounded-0 rounded-end-2">
                    <i class="ti ti-settings ti-md"></i>
                </button>
                <div class="template-customizer-inner">
                    <div class="template-customizer-header">
                        <h4 class="template-customizer-t-panel_header">TEMPLATE CUSTOMIZER</h4>
                        <p class="template-customizer-t-panel_sub_header mb-0">Customize and preview in real time.</p>
                        <button class="template-customizer-close-btn btn-close"></button>
                    </div>
                    <hr class="m-0">
                    <div class="template-customizer-scroller">
                        <div class="template-customizer-scroller-inner">
                            <div class="template-customizer-theming">
                                <h5 class="template-customizer-t-theming_header text-uppercase ps-4 ms-n4 py-2">Theming</h5>
                                <div class="template-customizer-style px-4 ms-n4 template-customizer-t-style_label">
                                    <label class="form-label visually-hidden">Style (Mode)</label>
                                    <div class="row row-cols-2">
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="styleRadios" id="styleRadiosLight" value="light">
                                                <label class="form-check-label template-customizer-t-style_switch_light" for="styleRadiosLight">Light</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="styleRadios" id="styleRadiosDark" value="dark">
                                                <label class="form-check-label template-customizer-t-style_switch_dark" for="styleRadiosDark">Dark</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="template-customizer-themes px-4 ms-n4 template-customizer-t-theme_label">
                                    <label for="customizerTheme" class="form-label visually-hidden">Themes</label>
                                    <h6 class="template-customizer-t-theme_header mt-2">Theme</h6>
                                    <div class="row row-cols-2 template-customizer-themes-options">
                                        </div>
                                </div>
                            </div>
                            <div class="template-customizer-layout">
                                <h5 class="template-customizer-t-layout_header text-uppercase ps-4 ms-n4 py-2">Layout</h5>
                                <div class="template-customizer-rtl px-4 ms-n4 template-customizer-t-rtl_label ${!this.settings.rtlAssetsEnabled ? 'd-none' : ''}">
                                    <label for="customizerRtl" class="form-label visually-hidden">RTL</label>
                                    <div class="row row-cols-2">
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="rtlRadios" id="rtlRadiosLtr" value="ltr">
                                                <label class="form-check-label" for="rtlRadiosLtr">LTR</label>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="rtlRadios" id="rtlRadiosRtl" value="rtl">
                                                <label class="form-check-label" for="rtlRadiosRtl">RTL</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        this.container = wrapper.firstChild;
        document.body.appendChild(this.container);

        // Setup Listeners for UI elements
        this._bindCustomizerUIActions();
        this.setLang(this.settings.lang, true); // Apply language to customizer UI
        this.update(); // Update control states
    }

    _bindCustomizerUIActions() {
        // Open/Close
        this.container.querySelector('.template-customizer-open-btn')?.addEventListener('click', () => this.container.classList.add('template-customizer-open'));
        this.container.querySelector('.template-customizer-close-btn')?.addEventListener('click', () => this.container.classList.remove('template-customizer-open'));

        // Style (Light/Dark)
        const styleRadios = this.container.querySelectorAll('input[name="styleRadios"]');
        styleRadios.forEach(radio => {
            if (radio.value === this.settings.style) radio.checked = true;
            radio.addEventListener('change', (e) => this.setStyle(e.target.value));
        });

        // RTL/LTR
        const rtlRadios = this.container.querySelectorAll('input[name="rtlRadios"]');
        rtlRadios.forEach(radio => {
            if ((radio.value === 'rtl' && this.settings.rtl) || (radio.value === 'ltr' && !this.settings.rtl)) {
                radio.checked = true;
            }
            radio.addEventListener('change', (e) => this.setRtl(e.target.value === 'rtl'));
        });
        if (!this.settings.controls.includes('rtl') || !this.settings.rtlAssetsEnabled) {
            this.container.querySelector('.template-customizer-rtl')?.classList.add('d-none');
        }


        // Themes
        const themesOptionsContainer = this.container.querySelector('.template-customizer-themes-options');
        if (themesOptionsContainer && this.settings.controls.includes('themes')) {
            themesOptionsContainer.innerHTML = ''; // Clear previous
            this.settings.availableThemes.forEach(theme => {
                const themeHTML = `
                    <div class="col-6 template-customizer-theme-item">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="themeRadios" id="themeRadios${theme.name}" value="${theme.name}" ${this.settings.theme.name === theme.name ? 'checked' : ''}>
                            <label class="form-check-label" for="themeRadios${theme.name}">${theme.title}</label>
                        </div>
                    </div>`;
                themesOptionsContainer.insertAdjacentHTML('beforeend', themeHTML);
            });
            themesOptionsContainer.querySelectorAll('input[name="themeRadios"]').forEach(radio => {
                radio.addEventListener('change', (e) => this.setTheme(e.target.value));
            });
        } else if(themesOptionsContainer) {
             themesOptionsContainer.closest('.template-customizer-themes').classList.add('d-none');
        }
        // Add event listeners for other controls (layoutType, fixedNavbar etc.) similarly
    }

    // --- Helper Methods ---
    _getSetting(key) {
        try {
            return localStorage.getItem(`templateCustomizer-${this._getLayoutName()}--${key}`);
        } catch (e) {
            console.warn(`TemplateCustomizer: Error reading localStorage for key '${key}'.`, e);
            return null;
        }
    }

    _setSetting(key, value) {
        try {
            localStorage.setItem(`templateCustomizer-${this._getLayoutName()}--${key}`, String(value));
        } catch (e) {
            console.warn(`TemplateCustomizer: Error writing to localStorage for key '${key}'.`, e);
        }
    }

    _getLayoutName() {
        return initialHtmlElement.getAttribute('data-template') || 'unknown-layout';
    }

    _getThemeByName(name, returnDefaultIfNotFound = false) {
        const found = this.settings.availableThemes.find(theme => theme.name === name);
        return found || (returnDefaultIfNotFound ? this.settings.availableThemes[0] : null);
    }

    _loadingState(show, isThemeLoading = false) {
      if (this.container) {
        this.container.classList.toggle(`template-customizer-loading${isThemeLoading ? '-theme' : ''}`, show);
      }
    }

    update() { /* Placeholder for updating control states based on current layout, e.g., disable fixed navbar if not applicable */ }
    _hasControls(controlsString, oneOf = false) { /* Placeholder */ return controlsString.split(' ').some(c => this.settings.controls.includes(c)); }
    get _isSSR() { return typeof window === 'undefined'; }

    destroy() {
        this._listeners.forEach(([el, evt, cb]) => el.removeEventListener(evt, cb));
        this._listeners = [];
        if (this.container && this.container.parentNode) {
            this.container.parentNode.removeChild(this.container);
        }
        this.container = null;
    }
}

TemplateCustomizer.THEMES = [
    { name: 'theme-default', title: 'Default' },
    { name: 'theme-semi-dark', title: 'Semi Dark' },
    { name: 'theme-bordered', title: 'Bordered' }
];

TemplateCustomizer.LANGUAGES = {
    en: { /* ... English translations as before ... */
        panel_header: 'TEMPLATE CUSTOMIZER', panel_sub_header: 'Customize and preview in real time',
        theming_header: 'THEMING', theme_header: 'Theme', style_label: 'Style (Mode)',
        style_switch_light: 'Light', style_switch_dark: 'Dark', layout_header: 'LAYOUT',
        layout_label: 'Menu Layout', layout_type_static: 'Static', layout_type_fixed: 'Fixed',
        rtl_label: 'RTL Direction', themes: { 'theme-default': 'Default', 'theme-semi-dark': 'Semi Dark', 'theme-bordered': 'Bordered' }
    },
    my: { // Bahasa Melayu translations
        panel_header: 'Penyesuai Templat', panel_sub_header: 'Suaikan dan pratonton dalam masa nyata',
        theming_header: 'Tema Global', theme_header: 'Skim Warna Tema', style_label: 'Gaya (Mod)',
        style_switch_light: 'Cerah', style_switch_dark: 'Gelap', layout_header: 'Tata Letak',
        layout_label: 'Tata Letak Menu', layout_type_static: 'Statik', layout_type_fixed: 'Tetap',
        rtl_label: 'Arah RTL', themes: { 'theme-default': 'Asal', 'theme-semi-dark': 'Separa Gelap', 'theme-bordered': 'Berbingkai' }
    },
    ar: { /* ... Arabic translations as before ... */
        panel_header: 'مخصص القالب', panel_sub_header: 'تخصيص ومعاينة في الوقت الحقيقي',
        theming_header: 'السمات', theme_header: 'السمة', style_label: 'النمط (الوضع)',
        style_switch_light: 'فاتح', style_switch_dark: 'داكن', layout_header: 'التخطيط',
        layout_label: 'تخطيط القائمة', layout_type_static: 'ثابت', layout_type_fixed: 'ثابت (مثبت)',
        rtl_label: 'اتجاه RTL', themes: { 'theme-default': 'افتراضي', 'theme-semi-dark': 'شبه داكن', 'theme-bordered': 'مُحدَّد' }
    }
};

// Export or attach to window if needed
// export { TemplateCustomizer };
// window.TemplateCustomizer = TemplateCustomizer; // If it's meant to be a global
