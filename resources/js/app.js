/**
 * Main application JavaScript file.
 * This file is compiled via Vite and included in the main application layout.
 */

// 1. Import Base CSS
// This line imports the main stylesheet. Ensure 'resources/css/app.css' exists
// and is configured (e.g., with Tailwind CSS if used).
import '../css/app.css';

// 2. Import Essential JavaScript Libraries
// Laravel's default bootstrap.js often sets up Axios.
// If the HRMS template uses Bootstrap's JS components, it would be imported here too.
// import './bootstrap'; // Uncomment if using Laravel's bootstrap.js

// Import and initialize Alpine.js, commonly used with Livewire for UI interactions.
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Note on Livewire:
// Livewire's core JavaScript is typically included via Blade directives (@livewireStyles and @livewireScripts)
// in your main layout file (e.g., resources/views/layouts/app.blade.php).
// Explicit import here is usually not needed for Livewire v3 with Vite unless you have specific advanced configurations.
// Example if manual Livewire init was needed (less common for v3):
// import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
// Livewire.start();


// 3. Custom Application-Specific JavaScript
// You can import custom JS modules or initialize libraries here.

// Example: Initializing an input masking library (assuming you install one like 'imask')
// import IMask from 'imask';
// window.IMask = IMask;
// document.addEventListener('DOMContentLoaded', () => {
//   // Example NRIC mask from MyMail form [cite: 181]
//   const nricElements = document.querySelectorAll('.nric-mask'); // Add this class to your inputs
//   nricElements.forEach(el => {
//     IMask(el, {
//       mask: '000000-00-0000'
//     });
//   });

//   // Example Mobile Phone Number mask [cite: 181]
//   const mobileElements = document.querySelectorAll('.mobile-mask'); // Add this class to your inputs
//   mobileElements.forEach(el => {
//     IMask(el, {
//       mask: '000-00000000' // Adjust mask as needed
//     });
//   });
// });


// Example: Setting up a basic auto-complete (conceptual)
// This would depend heavily on the chosen library or custom implementation.
// import './modules/autocomplete'; // Assuming you create an autocomplete.js module


// Log to confirm the script is loaded (for debugging)
console.log('MOTAC Integrated Resource Management System app.js loaded.');
