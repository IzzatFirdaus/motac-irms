/**
 * MOTAC Integrated Resource Management System - Main JS Entry
 * This file is bundled via Vite and loaded in your main application layout.
 *
 * Purpose:
 * - Import base CSS
 * - Initialize essential JS libraries (Alpine.js)
 * - Setup window-wide utilities
 * - Provide hooks for custom, feature-specific JS modules
 *
 * Documentation: Comments included for maintainability and developer onboarding.
 */

// --------------------------------------------------
// 1. Import Base CSS (Tailwind, custom, etc.)
// --------------------------------------------------
import '../css/app.css'; // Ensure resources/css/app.css exists and is configured.

// --------------------------------------------------
// 2. Import & Initialize Essential JS Libraries
// --------------------------------------------------

// Alpine.js - lightweight UI reactivity, commonly used with Livewire
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Uncomment below if using Laravel's default bootstrap.js (Axios, Bootstrap JS, etc.)
// import './bootstrap';

// Livewire Notes:
// Livewire's JS is typically loaded via Blade (@livewireScripts), not here. Only import if doing advanced custom builds.
// See: https://livewire.laravel.com/docs/installation

// --------------------------------------------------
// 3. Setup Custom Global Utilities (Optional)
// --------------------------------------------------

// Example: Input Masking with IMask (install via npm if needed)
// import IMask from 'imask';
// window.IMask = IMask;
// document.addEventListener('DOMContentLoaded', () => {
//   document.querySelectorAll('.nric-mask').forEach(el => IMask(el, { mask: '000000-00-0000' }));
//   document.querySelectorAll('.mobile-mask').forEach(el => IMask(el, { mask: '000-00000000' }));
// });

// Example: Import custom modules/features for the app
// import './modules/autocomplete'; // If you have custom autocomplete JS

// --------------------------------------------------
// 4. Application Ready Hook
// --------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
  // Place any site-wide JS initialization here.
  // Example: Set up global event listeners, UI tweaks, etc.

  // Example: Show a toast when the page loads (using a library or custom function)
  // if (window.showWelcomeToast) window.showWelcomeToast('Welcome to MOTAC IRMS!');
});

// --------------------------------------------------
// 5. Debugging Confirmation (Remove for production)
// --------------------------------------------------
console.log('resources/js/app.js loaded for MOTAC Integrated Resource Management System.');
