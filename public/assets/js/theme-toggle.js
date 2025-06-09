/**
 * Dark Mode Toggle Script
 *
 * This script handles toggling the Bootstrap 5 color theme between 'light' and 'dark'.
 * It persists the user's choice using localStorage and defaults to their
 * system preference if no choice has been made.
 */
(() => {
  'use strict';

  // Function to get the stored theme from localStorage
  const getStoredTheme = () => localStorage.getItem('theme');

  // Function to store the chosen theme in localStorage
  const setStoredTheme = theme => localStorage.setItem('theme', theme);

  // Function to determine the preferred theme (stored, or system preference)
  const getPreferredTheme = () => {
    const storedTheme = getStoredTheme();
    if (storedTheme) {
      return storedTheme;
    }
    // Check system preference
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  };

  // Function to set the theme on the <html> element and update the icon
  const setTheme = theme => {
    if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      document.documentElement.setAttribute('data-bs-theme', 'dark');
    } else {
      document.documentElement.setAttribute('data-bs-theme', theme);
    }

    // Update the icon for all togglers
    document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
      const sunIcon = toggle.querySelector('.bi-sun-fill');
      const moonIcon = toggle.querySelector('.bi-moon-stars-fill');

      if (theme === 'dark') {
        sunIcon.style.display = 'inline-block';
        moonIcon.style.display = 'none';
      } else {
        sunIcon.style.display = 'none';
        moonIcon.style.display = 'inline-block';
      }
    });
  };

  // Set the theme on initial page load
  setTheme(getPreferredTheme());

  // Add event listeners to all theme togglers on the page
  window.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
      toggle.addEventListener('click', (event) => {
        event.preventDefault();
        const currentTheme = getStoredTheme() || getPreferredTheme();
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        setStoredTheme(newTheme);
        setTheme(newTheme);
      });
    });
  });
})();
