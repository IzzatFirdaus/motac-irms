/**
 * Main Application Script
 */

'use strict';

// Global variables typically initialized by window.Helpers based on HTML data attributes
// Ensure window.Helpers, window.templateName, window.assetsPath, window.baseUrl, window.config are available
// window.Helpers is initialized in scriptsIncludes.blade.php -> helpers.js
// window.templateCustomizer is initialized in scriptsIncludes.blade.php -> template-customizer.js
// window.config is initialized in scriptsIncludes.blade.php -> config.js (if used)
// assetsPath and baseUrl are usually part of window.Helpers or set globally in commonMaster.blade.php

let isRtl = window.Helpers?.isRtl() ?? false;
let isDarkStyle = window.Helpers?.isDarkStyle() ?? false;
let menu, animate; // Menu instance
let isHorizontalLayout = false;

if (document.getElementById('layout-menu')) {
    isHorizontalLayout = document.getElementById('layout-menu').classList.contains('menu-horizontal');
}

(function () {
    // Initialize waves effect if lib is loaded
    if (typeof Waves !== 'undefined') {
        Waves.init();
        Waves.attach(".btn[class*='btn-']:not([class*='btn-outline-']):not([class*='btn-label-'])", ['waves-light']);
        Waves.attach("[class*='btn-outline-']");
        Waves.attach("[class*='btn-label-']");
        Waves.attach('.pagination .page-item .page-link');
        console.log('Waves initialized.');
    } else {
        console.warn('Waves library not found.');
    }

    // Initialize menu
    //-----------------
    const layoutMenuEls = document.querySelectorAll('#layout-menu');
    layoutMenuEls.forEach(function (element) {
        // System Design 3.3: MenuServiceProvider loads menu data. Client-side Menu class handles interactions.
        menu = new Menu(element, {
            orientation: isHorizontalLayout ? 'horizontal' : 'vertical',
            closeChildren: isHorizontalLayout, // Simplified for horizontal
            showDropdownOnHover: (localStorage.getItem(`templateCustomizer-${window.templateName}--ShowDropdownOnHover`) // Check if window.templateName is globally available
                ? localStorage.getItem(`templateCustomizer-${window.templateName}--ShowDropdownOnHover`) === 'true'
                : window.templateCustomizer?.settings.defaultShowDropdownOnHover) ?? true
        });
        window.Helpers.scrollToActive((animate = false)); // Animate scroll to active item
        window.Helpers.mainMenu = menu; // Make menu instance globally accessible via Helpers
        console.log('Menu initialized.');
    });

    // Initialize menu togglers
    const menuToggler = document.querySelectorAll('.layout-menu-toggle');
    menuToggler.forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            window.Helpers.toggleCollapsed();
            // Persist menu state if enabled in config.js (window.config.enableMenuLocalStorage)
            if (window.config?.enableMenuLocalStorage && !window.Helpers.isSmallScreen()) {
                try {
                    localStorage.setItem(
                        `templateCustomizer-${window.templateName}--LayoutCollapsed`,
                        String(window.Helpers.isCollapsed())
                    );
                } catch (e) {
                    console.error('Error saving menu collapsed state to localStorage:', e);
                }
            }
        });
    });

    // Menu swipe gestures for mobile
    if (window.Helpers.isSmallScreen()) {
        window.Helpers.swipeIn('.drag-target', function () {
            window.Helpers.setCollapsed(false);
        });
        window.Helpers.swipeOut('#layout-menu', function () {
            window.Helpers.setCollapsed(true);
        });
    }

    // Menu inner shadow display based on scroll
    const menuInnerContainer = document.querySelector('.menu-inner');
    const menuInnerShadow = document.querySelector('.menu-inner-shadow');
    if (menuInnerContainer && menuInnerShadow) {
        menuInnerContainer.addEventListener('ps-scroll-y', function () { // Assumes PerfectScrollbar custom event
            menuInnerShadow.style.display = this.scrollTop > 0 ? 'block' : 'none';
        });
    }

    // Style Switcher (Light/Dark Mode)
    //---------------------------------
    // This interacts with template-customizer.js
    const styleSwitcherToggleEl = document.querySelector('.style-switcher-toggle');
    if (window.templateCustomizer && styleSwitcherToggleEl) {
        styleSwitcherToggleEl.addEventListener('click', function () {
            if (window.Helpers.isLightStyle()) {
                window.templateCustomizer.setStyle('dark'); // Triggers reload via template-customizer.js
            } else {
                window.templateCustomizer.setStyle('light'); // Triggers reload
            }
        });

        // Set initial icon and tooltip for style switcher
        const switcherIcon = styleSwitcherToggleEl.querySelector('i');
        if (switcherIcon) {
            if (window.Helpers.isLightStyle()) {
                switcherIcon.classList.add('ti-moon-stars');
                new bootstrap.Tooltip(styleSwitcherToggleEl, {
                    title: window.templateCustomizer.L10N?.style_switch_dark || 'Dark mode', // Use translations if available
                    fallbackPlacements: ['bottom']
                });
                switchImage('light');
            } else {
                switcherIcon.classList.add('ti-sun');
                new bootstrap.Tooltip(styleSwitcherToggleEl, {
                    title: window.templateCustomizer.L10N?.style_switch_light || 'Light mode',
                    fallbackPlacements: ['bottom']
                });
                switchImage('dark');
            }
        }
    } else if (styleSwitcherToggleEl) {
        styleSwitcherToggleEl.parentElement.remove(); // Remove if customizer not available
    }

    // Function to switch images based on style (data-app-light-img, data-app-dark-img)
    function switchImage(style) {
        const imageElements = document.querySelectorAll(`[data-app-${style}-img]`);
        imageElements.forEach(imageEl => {
            const imageName = imageEl.getAttribute(`data-app-${style}-img`);
            // Ensure window.assetsPath is correctly defined, usually in commonMaster.blade.php or helpers.js
            imageEl.src = `${window.Helpers?.settings.assetsPath ?? '/assets/'}img/illustrations/${imageName}`;
        });
    }

    // Language Dropdown Flag Update (Initial state based on server-rendered HTML lang)
    // The actual language switch is handled by a page reload via LanguageController.php
    // This ensures the flag icon in the navbar reflects the current language.
    //
    function updateNavbarLangFlag() {
        const currentHtmlLang = document.documentElement.getAttribute('lang'); // e.g., 'en', 'my', 'ar'
        const langDropdownElement = document.querySelector('.dropdown-language');

        if (currentHtmlLang && langDropdownElement) {
            const languageLinks = langDropdownElement.querySelectorAll('.dropdown-item[data-language]');
            let selectedFlagClass = 'fi fi-us'; // Default to US flag

            languageLinks.forEach(link => {
                link.classList.remove('active'); // Use 'active' class as per navbar.blade.php
                if (link.getAttribute('data-language') === currentHtmlLang) {
                    link.classList.add('active');
                    const iconElement = link.querySelector('i.fis'); // Selector for flag icon
                    if (iconElement) {
                        selectedFlagClass = iconElement.className;
                    }
                }
            });

            const mainFlagIcon = langDropdownElement.querySelector('.dropdown-toggle i.fis');
            if (mainFlagIcon) {
                mainFlagIcon.className = selectedFlagClass; // Update the main flag icon
            }
        }
    }
    updateNavbarLangFlag(); // Call on page load


    // REMOVED: Client-side notification marking. This is now handled by Livewire component Navbar.php
    // to ensure server state is the source of truth.

    // Initialize Bootstrap Tooltips
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Initialize Accordion active class handling
    const accordionElements = Array.from(document.querySelectorAll('.accordion'));
    accordionElements.forEach(accordionEl => {
        accordionEl.addEventListener('show.bs.collapse', event => event.target.closest('.accordion-item').classList.add('active'));
        accordionEl.addEventListener('hide.bs.collapse', event => event.target.closest('.accordion-item').classList.remove('active'));
    });

    // Add .dropdown-menu-end to RTL dropdowns
    if (isRtl) {
        const navbarDropdownMenus = document.querySelectorAll('#layout-navbar .dropdown-menu');
        navbarDropdownMenus.forEach(menu => window.Helpers?._addClass('dropdown-menu-end', menu)); // Check if _addClass exists
    }

    // Auto update layout based on screen size
    window.Helpers?.setAutoUpdate?.(true);

    // Initialize Password Toggle, Speech To Text, Navbar Dropdown Scrollbar
    window.Helpers?.initPasswordToggle?.();
    window.Helpers?.initSpeechToText?.(); // If this feature is used
    window.Helpers?.initNavbarDropdownScrollbar?.();

    // Window resize event listener
    window.addEventListener('resize', function () {
        if (window.innerWidth >= window.Helpers.LAYOUT_BREAKPOINT) {
            const searchInputWrapper = document.querySelector('.search-input-wrapper');
            if (searchInputWrapper) {
                searchInputWrapper.classList.add('d-none');
                const searchInput = searchInputWrapper.querySelector('.search-input');
                if (searchInput) searchInput.value = '';
            }
        }
        // Horizontal Layout: Update menu based on window size
        if (isHorizontalLayout && menu) { // Check if menu is initialized
            setTimeout(function () {
                if (window.innerWidth < window.Helpers.LAYOUT_BREAKPOINT) {
                    if (menu.orientation === 'horizontal') menu.switchMenu('vertical');
                } else {
                    if (menu.orientation === 'vertical') menu.switchMenu('horizontal');
                }
            }, 100);
        }
    }, true);

    // Manage menu expanded/collapsed state with localStorage and TemplateCustomizer
    if (!isHorizontalLayout && !window.Helpers.isSmallScreen()) {
        if (window.templateCustomizer?.settings.defaultMenuCollapsed) {
            window.Helpers.setCollapsed(true, false);
        }
        if (window.config?.enableMenuLocalStorage) {
            try {
                const storedLayoutCollapsed = localStorage.getItem(`templateCustomizer-${window.templateName}--LayoutCollapsed`);
                if (storedLayoutCollapsed) {
                    window.Helpers.setCollapsed(storedLayoutCollapsed === 'true', false);
                }
            } catch (e) {
                console.error('Error reading menu collapsed state from localStorage:', e);
            }
        }
    }
})();

// jQuery dependent code (Navbar Search with Typeahead)
// Kept as per original structure. If removing jQuery, this needs a vanilla JS rewrite.
if (typeof $ !== 'undefined') {
    $(function () {
        window.Helpers?.initSidebarToggle?.(); // Initialize sidebar toggle if it exists

        const searchToggler = $('.search-toggler');
        const searchInputWrapper = $('.search-input-wrapper');
        const searchInput = $('.search-input');
        const contentBackdrop = $('.content-backdrop');

        if (searchToggler.length) {
            searchToggler.on('click', function () {
                if (searchInputWrapper.length) {
                    searchInputWrapper.toggleClass('d-none');
                    searchInput.trigger('focus');
                }
            });
        }

        $(document).on('keydown', function (event) {
            if (event.ctrlKey && event.key === '/') { // More modern key check
                event.preventDefault();
                if (searchInputWrapper.length) {
                    searchInputWrapper.toggleClass('d-none');
                    searchInput.trigger('focus');
                }
            }
        });

        searchInput.on('focus', function () {
            if (searchInputWrapper.hasClass('container-xxl')) { // Ensure .twitter-typeahead exists if this class is added
                searchInputWrapper.find('.twitter-typeahead').addClass('container-xxl');
            }
        });

        if (searchInput.length) {
            const filterConfig = function (data) {
                return function findMatches(q, cb) {
                    let matches = [];
                    const queryLower = q.toLowerCase();
                    data.forEach(function (i) { // Changed filter to forEach for clarity
                        const nameLower = i.name.toLowerCase();
                        if (nameLower.startsWith(queryLower)) {
                            matches.push(i);
                        } else if (nameLower.includes(queryLower)) {
                            matches.push(i);
                        }
                    });
                    // Sort matches if needed (original had a sort for includes only)
                    matches.sort((a, b) => (a.name < b.name ? -1 : (a.name > b.name ? 1 : 0)));
                    cb(matches);
                };
            };

            // Use window.assetsPath from Helpers for JSON path construction
            const assetsPath = window.Helpers?.settings.assetsPath ?? '/assets/';
            const searchJsonFile = $('#layout-menu').hasClass('menu-horizontal') ? 'search-horizontal.json' : 'search-vertical.json';

            $.ajax({
                url: `${assetsPath}json/${searchJsonFile}`,
                dataType: 'json',
                async: true // Generally better to load async
            }).done(function (searchData) {
                searchInput.each(function () {
                    const $this = $(this);
                    $this.typeahead({
                        hint: false,
                        classNames: {
                            menu: 'tt-menu navbar-search-suggestion',
                            cursor: 'active',
                            suggestion: 'suggestion d-flex justify-content-between px-3 py-2 w-100'
                        }
                    },
                    // Pages
                    {
                        name: 'pages', display: 'name', limit: 5, source: filterConfig(searchData.pages || []),
                        templates: { /* ... templates as before, ensure baseUrl is globally available ... */ }
                    },
                    // Files
                    {
                        name: 'files', display: 'name', limit: 4, source: filterConfig(searchData.files || []),
                        templates: { /* ... templates as before ... */ }
                    },
                    // Members
                    {
                        name: 'members', display: 'name', limit: 4, source: filterConfig(searchData.members || []),
                        templates: { /* ... templates as before ... */ }
                    })
                    .bind('typeahead:render', function () { contentBackdrop.addClass('show').removeClass('fade'); })
                    .bind('typeahead:select', function (ev, suggestion) {
                        if (suggestion.url && suggestion.url !== 'javascript:;') {
                             // Ensure window.baseUrl is correctly set (usually in commonMaster or by Helpers)
                            window.location = (window.Helpers?.settings.baseUrl ?? '') + suggestion.url;
                        }
                    })
                    .bind('typeahead:close', function () {
                        searchInput.val(''); $this.typeahead('val', '');
                        searchInputWrapper.addClass('d-none');
                        contentBackdrop.addClass('fade').removeClass('show');
                    });
                });

                // PerfectScrollbar for search results
                $('.navbar-search-suggestion').each(function () {
                   new PerfectScrollbar($(this)[0], {
                        wheelPropagation: false,
                        suppressScrollX: true
                    });
                });
                 searchInput.on('keyup', function () { // This might need to target the scrollable container if psSearch not global
                    const psInstance = PerfectScrollbar.instances.get($('.navbar-search-suggestion')[0]);
                    if(psInstance) psInstance.update();
                });


            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error("Failed to load search JSON:", textStatus, errorThrown);
            });

            searchInput.on('keyup', function () {
                if (searchInput.val() === '') {
                    contentBackdrop.addClass('fade').removeClass('show');
                }
            });
        }
    });
}
