import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Vite configuration for MOTAC IRMS
export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/js/app.js',
        'resources/js/laravel-user-management.js'
      ],
      refresh: true
    })
  ],
  resolve: {
    alias: {
      '@': '/resources/js'
    }
  }
});
