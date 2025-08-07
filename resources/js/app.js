/**
 * MOTAC Integrated Resource Management System - Main JS Entry Point
 * This file is bundled via Vite and loaded in your main application layout.
 *
 * Purpose:
 * - Import base CSS frameworks and custom styles
 * - Initialize essential JS libraries (Alpine.js, Bootstrap)
 * - Setup window-wide utilities and global configurations
 * - Provide hooks for custom, feature-specific JS modules
 * - Handle application-wide event listeners and initialization
 *
 * Last Updated: 2025-08-07 14:32:48 UTC
 * Updated By: IzzatFirdaus
 *
 * Documentation: Comments included for maintainability and developer onboarding.
 */

// --------------------------------------------------
// 1. Import Base CSS Frameworks and Custom Styles
// --------------------------------------------------
// Import main application CSS (includes Tailwind, Bootstrap, or custom framework)
import '../css/app.css';

// Import any additional CSS modules if needed
// import '../css/components.css';
// import '../css/utilities.css';

// --------------------------------------------------
// 2. Import & Initialize Essential JS Libraries
// --------------------------------------------------

// Alpine.js - Lightweight UI reactivity framework, commonly used with Livewire
// Provides declarative JavaScript functionality for interactive components
import Alpine from 'alpinejs';

// Make Alpine available globally for use in Blade templates and other scripts
window.Alpine = Alpine;

// Start Alpine.js after DOM is ready
Alpine.start();

// Bootstrap JS (uncomment if using Bootstrap components that require JavaScript)
// import * as bootstrap from 'bootstrap';
// window.bootstrap = bootstrap;

// Axios HTTP client (uncomment if making API requests)
// import axios from 'axios';
// window.axios = axios;
// window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Laravel's default bootstrap.js configuration (uncomment if needed)
// This typically includes Axios setup, CSRF token configuration, and Echo setup
// import './bootstrap';

// --------------------------------------------------
// 3. Setup Custom Global Utilities and Configurations
// --------------------------------------------------

// MOTAC-specific global configurations
window.MOTAC = {
    // Application metadata
    app: {
        name: 'MOTAC Integrated Resource Management System',
        version: '1.0.0',
        environment: process.env.NODE_ENV || 'production'
    },

    // Global utility functions
    utils: {
        // Format currency for Malaysian Ringgit
        formatCurrency: function(amount) {
            return new Intl.NumberFormat('ms-MY', {
                style: 'currency',
                currency: 'MYR'
            }).format(amount);
        },

        // Format dates for Malaysian locale
        formatDate: function(date, options = {}) {
            const defaultOptions = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                timeZone: 'Asia/Kuala_Lumpur'
            };
            return new Intl.DateTimeFormat('ms-MY', { ...defaultOptions, ...options }).format(new Date(date));
        },

        // Show notification toast (can integrate with SweetAlert2 or similar)
        showNotification: function(message, type = 'info') {
            // Implementation depends on your notification library
            console.log(`[${type.toUpperCase()}] ${message}`);

            // Example with SweetAlert2 (uncomment if using SweetAlert2)
            // if (window.Swal) {
            //     window.Swal.fire({
            //         icon: type,
            //         title: message,
            //         toast: true,
            //         position: 'top-end',
            //         showConfirmButton: false,
            //         timer: 3000
            //     });
            // }
        }
    }
};

// --------------------------------------------------
// 4. Input Masking and Form Enhancements
// --------------------------------------------------

// Malaysian-specific input masks using IMask (install via: npm install imask)
// Uncomment and install IMask if you need input masking functionality
/*
import IMask from 'imask';
window.IMask = IMask;

// Initialize input masks when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Malaysian NRIC/MyKad number format: 123456-12-1234
    document.querySelectorAll('.nric-mask').forEach(el => {
        IMask(el, {
            mask: '000000-00-0000',
            placeholder: 'YYMMDD-PB-###G'
        });
    });

    // Malaysian mobile number format: 01X-XXXXXXXX
    document.querySelectorAll('.mobile-mask').forEach(el => {
        IMask(el, {
            mask: '000-00000000',
            placeholder: '01X-XXXXXXXX'
        });
    });

    // Malaysian landline format: 0X-XXXXXXX
    document.querySelectorAll('.landline-mask').forEach(el => {
        IMask(el, {
            mask: '00-0000000',
            placeholder: '0X-XXXXXXX'
        });
    });

    // Postal code format: XXXXX
    document.querySelectorAll('.postcode-mask').forEach(el => {
        IMask(el, {
            mask: '00000',
            placeholder: 'XXXXX'
        });
    });
});
*/

// --------------------------------------------------
// 5. Application-Wide Event Listeners and Initialization
// --------------------------------------------------

// Main application initialization when DOM is fully loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize application-wide features
    initializeApplicationFeatures();

    // Setup global event listeners
    setupGlobalEventListeners();

    // Initialize accessibility features
    initializeAccessibilityFeatures();

    // Setup development/debugging features (only in development)
    if (window.MOTAC.app.environment === 'development') {
        setupDevelopmentFeatures();
    }
});

/**
 * Initialize core application features
 * This function sets up common functionality used across the application
 */
function initializeApplicationFeatures() {
    // Setup CSRF token for all AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken && window.axios) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken.getAttribute('content');
    }

    // Initialize tooltips (if using Bootstrap)
    if (window.bootstrap) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new window.bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Initialize popovers (if using Bootstrap)
    if (window.bootstrap) {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new window.bootstrap.Popover(popoverTriggerEl);
        });
    }

    // Auto-hide alerts after 5 seconds
    const autoHideAlerts = document.querySelectorAll('.alert[data-auto-dismiss]');
    autoHideAlerts.forEach(alert => {
        const delay = parseInt(alert.dataset.autoDispatch) || 5000;
        setTimeout(() => {
            if (window.bootstrap && window.bootstrap.Alert) {
                const bsAlert = new window.bootstrap.Alert(alert);
                bsAlert.close();
            } else {
                alert.style.display = 'none';
            }
        }, delay);
    });
}

/**
 * Setup global event listeners
 * This function sets up event listeners that work across the entire application
 */
function setupGlobalEventListeners() {
    // Handle form submissions with loading states
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form.tagName === 'FORM' && !form.classList.contains('no-loading')) {
            const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitButton) {
                // Add loading state to submit button
                submitButton.disabled = true;
                const originalText = submitButton.textContent || submitButton.value;
                submitButton.textContent = 'Processing...';
                submitButton.classList.add('loading');

                // Reset button state after form submission completes (or fails)
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                    submitButton.classList.remove('loading');
                }, 3000);
            }
        }
    });

    // Handle external links (open in new tab)
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[href^="http"]');
        if (link && !link.hostname.includes(window.location.hostname)) {
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
        }
    });

    // Handle confirmation dialogs
    document.addEventListener('click', (e) => {
        const element = e.target.closest('[data-confirm]');
        if (element) {
            const message = element.dataset.confirm || 'Are you sure?';
            if (!confirm(message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        }
    });
}

/**
 * Initialize accessibility features
 * This function sets up accessibility enhancements for better user experience
 */
function initializeAccessibilityFeatures() {
    // Skip to main content link
    const skipLink = document.querySelector('.skip-to-main');
    if (skipLink) {
        skipLink.addEventListener('click', (e) => {
            e.preventDefault();
            const mainContent = document.querySelector('#main-content, main, .main-content');
            if (mainContent) {
                mainContent.focus();
                mainContent.scrollIntoView();
            }
        });
    }

    // Keyboard navigation for dropdowns
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            // Close any open dropdowns or modals
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(dropdown => {
                if (window.bootstrap && window.bootstrap.Dropdown) {
                    const toggle = dropdown.previousElementSibling;
                    if (toggle) {
                        window.bootstrap.Dropdown.getInstance(toggle)?.hide();
                    }
                }
            });
        }
    });

    // Announce page changes to screen readers
    const announcer = document.createElement('div');
    announcer.setAttribute('aria-live', 'polite');
    announcer.setAttribute('aria-atomic', 'true');
    announcer.className = 'sr-only';
    document.body.appendChild(announcer);

    // Store announcer globally for use by other scripts
    window.MOTAC.accessibility = {
        announce: function(message) {
            announcer.textContent = message;
        }
    };
}

/**
 * Setup development and debugging features
 * This function is only called in development environment
 */
function setupDevelopmentFeatures() {
    // Log when Alpine.js components are initialized
    document.addEventListener('alpine:init', () => {
        console.log('Alpine.js initialized');
    });

    // Log Livewire events (if Livewire is present)
    if (window.Livewire) {
        window.Livewire.on('component.initialized', (component) => {
            console.log('Livewire component initialized:', component);
        });
    }

    // Add development toolbar or debugging info
    console.log('MOTAC IRMS Development Mode Active');
    console.log('Environment:', window.MOTAC.app.environment);
    console.log('Alpine.js version:', Alpine.version);
}

// --------------------------------------------------
// 6. Module Imports for Feature-Specific Functionality
// --------------------------------------------------

// Import custom modules for specific features
// These should be separate files for better organization and maintainability

// Example: Import autocomplete functionality
// import './modules/autocomplete';

// Example: Import file upload handlers
// import './modules/file-upload';

// Example: Import data table enhancements
// import './modules/datatables-config';

// Example: Import notification system
// import './modules/notifications';

// Example: Import form validation enhancements
// import './modules/form-validation';

// --------------------------------------------------
// 7. Error Handling and Logging
// --------------------------------------------------

// Global error handler for unhandled JavaScript errors
window.addEventListener('error', (e) => {
    console.error('Global JavaScript Error:', e.error);

    // In production, you might want to send errors to a logging service
    if (window.MOTAC.app.environment === 'production') {
        // Example: Send to logging service
        // logErrorToService(e.error);
    }
});

// Global handler for unhandled promise rejections
window.addEventListener('unhandledrejection', (e) => {
    console.error('Unhandled Promise Rejection:', e.reason);

    // Prevent the default browser behavior (logging to console)
    e.preventDefault();

    // In production, you might want to send errors to a logging service
    if (window.MOTAC.app.environment === 'production') {
        // Example: Send to logging service
        // logErrorToService(e.reason);
    }
});

// --------------------------------------------------
// 8. Application Ready Confirmation and Debugging
// --------------------------------------------------

// Confirm that the main application JavaScript has loaded successfully
console.log(`✅ MOTAC IRMS Main Application JavaScript loaded successfully at ${new Date().toISOString()}`);
console.log(`📱 Application: ${window.MOTAC.app.name} v${window.MOTAC.app.version}`);
console.log(`🌍 Environment: ${window.MOTAC.app.environment}`);

// Dispatch custom event to indicate app.js is ready
document.dispatchEvent(new CustomEvent('motac:app:ready', {
    detail: {
        timestamp: new Date().toISOString(),
        version: window.MOTAC.app.version,
        environment: window.MOTAC.app.environment
    }
}));

// --------------------------------------------------
// 9. Performance Monitoring (Optional)
// --------------------------------------------------

// Monitor and log performance metrics
if ('performance' in window) {
    window.addEventListener('load', () => {
        // Log page load performance
        const perfData = performance.getEntriesByType('navigation')[0];
        if (perfData) {
            console.log(`📊 Page Load Performance:
                DNS Lookup: ${Math.round(perfData.domainLookupEnd - perfData.domainLookupStart)}ms
                TCP Connection: ${Math.round(perfData.connectEnd - perfData.connectStart)}ms
                Server Response: ${Math.round(perfData.responseEnd - perfData.requestStart)}ms
                DOM Content Loaded: ${Math.round(perfData.domContentLoadedEventEnd - perfData.navigationStart)}ms
                Page Load Complete: ${Math.round(perfData.loadEventEnd - perfData.navigationStart)}ms
            `);
        }
    });
}

// --------------------------------------------------
// 10. Cleanup and Optimization
// --------------------------------------------------

// Clean up event listeners and resources when page is unloaded
window.addEventListener('beforeunload', () => {
    // Perform any necessary cleanup
    console.log('🧹 Cleaning up MOTAC IRMS application resources...');

    // Example: Cancel any pending requests
    // if (window.abortController) {
    //     window.abortController.abort();
    // }
});
