// Sidebar submenu toggle for MOTAC IRMS vertical menu
// Enhanced for accessibility, keyboard navigation, and correct scoping
document.addEventListener('DOMContentLoaded', function () {
    // Only operate within the sidebar, scoped to .motac-vertical-menu
    var sidebar = document.querySelector('.motac-vertical-menu');
    if (!sidebar) return;

    // Add click/tap toggling for submenus (for mouse and touch)
    sidebar.querySelectorAll('.menu-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            var parent = toggle.closest('.menu-item');
            var isOpen = parent.classList.toggle('open');
            // Set aria-expanded for accessibility
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        // Also support keyboard navigation (space or enter toggles submenu)
        toggle.addEventListener('keydown', function (e) {
            if (e.key === ' ' || e.key === 'Enter') {
                e.preventDefault();
                toggle.click();
            }
        });
    });

    // Optional: allow only one open submenu at a time (accordion style)
    // Uncomment to enable accordion behavior
    /*
    sidebar.querySelectorAll('.menu-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            var parent = toggle.closest('.menu-item');
            sidebar.querySelectorAll('.menu-item.open').forEach(function (item) {
                if (item !== parent) {
                    item.classList.remove('open');
                    var submenuToggle = item.querySelector('.menu-toggle');
                    if (submenuToggle) {
                        submenuToggle.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        });
    });
    */

    // Optional: Add focus styling for accessibility
    sidebar.querySelectorAll('.menu-link, .menu-toggle').forEach(function (link) {
        link.addEventListener('focus', function () {
            link.classList.add('focus');
        });
        link.addEventListener('blur', function () {
            link.classList.remove('focus');
        });
    });
});
